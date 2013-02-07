<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model\Site;
use Vivo\CMS\Exception\InvalidArgumentException;
use Vivo\CMS\Model;
use Vivo\CMS\Workflow;
use Vivo\CMS\Exception;
use Vivo\CMS\UuidConvertor\UuidConvertorInterface;
use Vivo\Repository\RepositoryInterface;
use Vivo\Indexer\Query\Parser\ParserInterface;
use Vivo\Indexer\Query\QueryInterface;
use Vivo\Indexer\QueryBuilder;
use Vivo\Indexer\IndexerInterface;
use Vivo\Indexer\QueryParams;
use Vivo\CMS\Indexer\IndexerHelperInterface;
use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\Uuid\GeneratorInterface as UuidGeneratorInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;

use Zend\Config;

use DateTime;

/**
 * Main business class for interact with CMS.
 */
class CMS
{
    /**
     * RegEx for UUID
     */
    const UUID_PATTERN = '[\d\w]{32}';

    /**
     * Repository
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * Indexer
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * Indexer Helper
     * @var IndexerHelperInterface
     */
    protected $indexerHelper;

    /**
     * Query Parsers
     * @var ParserInterface
     */
    protected $queryParser;

    /**
     * Query Builder
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * UUID Convertor
     * @var UuidConvertorInterface
     */
    protected $uuidConvertor;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * Path builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Constructor
     * @param RepositoryInterface $repository
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param IndexerHelperInterface $indexerHelper
     * @param \Vivo\Indexer\QueryBuilder $qb
     * @param \Vivo\Indexer\Query\Parser\ParserInterface $queryParser
     * @param \Vivo\CMS\UuidConvertor\UuidConvertorInterface $uuidConvertor
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     */
    public function __construct(RepositoryInterface $repository,
                                IndexerInterface $indexer,
                                IndexerHelperInterface $indexerHelper,
                                QueryBuilder $qb,
                                ParserInterface $queryParser,
                                UuidConvertorInterface $uuidConvertor,
                                UuidGeneratorInterface $uuidGenerator,
                                PathBuilderInterface $pathBuilder)
    {
        $this->repository       = $repository;
        $this->indexer          = $indexer;
        $this->indexerHelper    = $indexerHelper;
        $this->qb               = $qb;
        $this->queryParser      = $queryParser;
        $this->uuidConvertor    = $uuidConvertor;
        $this->uuidGenerator    = $uuidGenerator;
        $this->pathBuilder      = $pathBuilder;
    }

    /**
     * Returns Site matching given hostname.
     * If no site matches the hostname, returns null
     * @param string $host
     * @return Model\Site|null
     */
    public function getSiteByHost($host)
    {
        $query      = $this->qb->cond($host, '\\hosts');
        $entities   = $this->getEntitiesByQuery($query, array('page_size' => 1));
        if (count($entities) == 1) {
            //Site found
            $site   = reset($entities);
        } else {
            //Site not found - fallback to traversing the repo (necessary for reindexing)
            $sites  = $this->getChildren(new Model\Folder(''));
            $site   = null;
            foreach ($sites as $siteIter) {
                /** @var $siteIter \Vivo\CMS\Model\Site */
                if (($siteIter instanceof Site) and (in_array($host, $siteIter->getHosts()))) {
                    $site   = $siteIter;
                    break;
                }
            }
        }
        return $site;
    }

    /**
     * @param string $name Site name.
     * @param string $domain Security domain.
     * @param array $hosts
     * @return Model\Site
     */
    public function createSite($name, $domain, array $hosts)
    {
        $sitePath   = $this->pathBuilder->buildStoragePath(array($name), true);
        $site       = new Model\Site($sitePath);
        $site->setDomain($domain);
        $site->setHosts($hosts);
        $rootPath   = $this->pathBuilder->buildStoragePath(array($name, 'ROOT'), true);
        $root = new Model\Document($rootPath);
        $root->setTitle('Home');
        $root->setWorkflow('Vivo\CMS\Workflow\Basic');
        $this->saveEntity($site, false);
        $this->setSiteConfig(array(), $site);
        $this->saveEntity($root,  false);
        $this->repository->commit();
        return $site;
    }

    /**
     * Returns site configuration as it is stored in the repository (ie not merged with module configs)
     * @param Model\Site $site
     * @return array
     */
    public function getSiteConfig(Model\Site $site)
    {
        try {
            $string = $this->repository->getResource($site, 'config.ini');
        } catch (\Vivo\Storage\Exception\IOException $e) {
            return array();
        }

        $reader = new Config\Reader\Ini();
        $config = $reader->fromString($string);

        return $config;
    }

    /**
     * Persists site config
     * @param array $config
     * @param Model\Site $site
     */
    public function setSiteConfig(array $config, Model\Site $site)
    {
        $writer = new Config\Writer\Ini();
        $writer->setRenderWithoutSectionsFlags(true);
        $iniString  = $writer->toString($config);
        $this->repository->saveResource($site, 'config.ini', $iniString);
    }

    /**
     * @param string $path Relative document path in site.
     * @param Model\Site $site
     * @return Model\Document
     */
    public function getSiteDocument($path, Model\Site $site)
    {
        $fullPath   = $this->pathBuilder->buildStoragePath(array($site->getPath(), 'ROOT', $path));
        $entity     = $this->repository->getEntity($fullPath);
        return $entity;
    }

    /**
     * @param Model\Document $document
     * @return \Vivo\CMS\Workflow\AbstractWorkflow
     */
    public function getWorkflow(Model\Document $document)
    {
        return Workflow\Factory::get($document->getWorkflow());
    }

    /**
     * Returns entity specified by path, UUID or symbolic reference
     * @param string $ident Path, UUID or symbolic reference
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return Model\Entity
     */
    public function getEntity($ident)
    {
        $uuid   = $this->getUuidFromEntityIdent($ident);
        if ($uuid) {
            //$ident is UUID or symbolic reference
            $path   = $this->uuidConvertor->getPath($uuid);
            if (!$path) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Cannot convert entity identifier '%s' (UUID = '%s') to path",
                            __METHOD__, $ident, $uuid));
            }
        } else {
            //Assume $ident is a path
            $path   = $ident;
        }
        return $this->repository->getEntity($path);
    }

    /**
     * @param Model\Folder $folder
     * @return \Vivo\CMS\Model\Entity[]
     */
    public function getChildren(Model\Folder $folder)
    {
        return $this->repository->getChildren($folder);
    }

    /**
     * @param Model\Folder $folder
     * @return Model\Folder
     */
    public function getParent(Model\Folder $folder)
    {
        return $this->repository->getParent($folder);
    }

    /**
     * Saves entity
     * The entity is prepared before saving into repository
     * @param Model\Entity $entity
     * @param bool $commit
     */
    public function saveEntity(Model\Entity $entity, $commit = true)
    {
        $entity = $this->prepareEntityForSaving($entity);
        $this->repository->saveEntity($entity);
        if ($commit) {
            $this->repository->commit();
        }
    }

    /**
     * Prepares entity to be saved
     * @param \Vivo\CMS\Model\Entity $entity
     * @return \Vivo\CMS\Model\Entity
     */
    protected function prepareEntityForSaving(Model\Entity $entity)
    {
        $now            = new DateTime();
        $sanitizedPath  = $this->pathBuilder->sanitize($entity->getPath());
        $entity->setPath($sanitizedPath);
        if (!$entity->getCreated() instanceof \DateTime) {
            $entity->setCreated($now);
        }
        if (!$entity->getCreatedBy()) {
            //TODO - what to do when an entity does not have its creator set?
            //$entity->setCreatedBy($username);
        }
        if(!$entity->getUuid()) {
            $entity->setUuid($this->uuidGenerator->create());
        }
        $entity->setModified($now);
        //TODO - set entity modifier
//        $entity->setModifiedBy($username);
        return $entity;
    }

    public function saveDocument(Model\Document $document/*, $parent = null*/)
    {
        /*
                if($parent != null && !$parent instanceof Model\Document && !$parent instanceof Model\Site) {
                    throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an instance of %s',
                        2, __METHOD__, implode(', ', array('Vivo\Model\Document', 'Vivo\Model\Site')))
                    );
                }
         */
        $options    = array(
            'published_content_types'   => $this->getPublishedContentTypes($document),
        );
        $this->saveEntity($document, $options);
    }

    /**
     * @param Model\Document $document
     * @param string $target Path.
     */
    public function moveDocument(Model\Document $document, $target)
    {
        $this->repository->moveEntity($document, $target);
        $this->repository->commit();
    }

    private function removeEntity(Model\Entity $entity)
    {
        $this->repository->deleteEntity($entity);
        $this->repository->commit();
    }

    public function removeDocument(Model\Document $document)
    {
        $this->removeEntity($document);
    }

    /**
     * Returns document for the given content
     * @param Model\Content $content
     * @return Model\Document
     */
    public function getContentDocument(Model\Content $content)
    {
        $path = $content->getPath();
        $components = $this->pathBuilder->getStoragePathComponents($path);
        array_pop($components);
        array_pop($components);
        $docPath    = $this->pathBuilder->buildStoragePath($components, true);
        $document = $this->repository->getEntity($docPath);
        if ($document instanceof Model\Document) {
            return $document;
        }
        return null;
    }

    public function addDocumentContent(Model\Document $document, Model\Content $content, $index = 0)
    {
        $path           = $document->getPath();
        $version        = count($this->getDocumentContents($document, $index));
        $components     = array($path, 'Contents' . $index, $version);
        $contentPath    = $this->pathBuilder->buildStoragePath($components, true);
        $content->setPath($contentPath);
        $content->setState(Workflow\AbstractWorkflow::STATE_NEW);
        $this->saveEntity($content);
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @param int $version
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return Model\Content
     */
    public function getDocumentContent(Model\Document $document, $index, $version/*, $state {PUBLISHED}*/)
    {
        if (!is_integer($version)) {
            throw new Exception\InvalidArgumentException(
                    sprintf(
                            'Argument %d passed to %s must be an type of %s, %s given',
                            2, __METHOD__, 'integer', gettype($version)));
        }
        if (!is_integer($index)) {
            throw new Exception\InvalidArgumentException(
                    sprintf(
                            'Argument %d passed to %s must be an type of %s, %s given',
                            3, __METHOD__, 'integer', gettype($index)));
        }
        $components = array(
            $document->getPath(),
            'Contents.',
            $index,
            $version,
        );
        $path   = $this->pathBuilder->buildStoragePath($components, true);
        $entity = $this->repository->getEntity($path);
        return $entity;
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return array
     */
    public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/)
    {
        if (!is_integer($index)) {
            throw new Exception\InvalidArgumentException(
                    sprintf(
                            'Argument %d passed to %s must be an type of integer, %s given',
                            2, __METHOD__, gettype($index)));
        }
        $pathElements   = array($document->getPath(), 'Contents.', $index);
        $path           = $this->pathBuilder->buildStoragePath($pathElements, true);
        return $this->repository->getChildren(new Model\Entity($path));
    }

    /**
     * @param Model\Content $content
     */
    public function publishContent(Model\Content $content)
    {
        $document   = $this->getContentDocument($content);
        $oldContent = $this->getPublishedContent($document, $content->getIndex());
        if ($oldContent) {
            $oldContent->setState(Workflow\AbstractWorkflow::STATE_ARCHIVED);
            $this->saveEntity($oldContent, false);
        }
        $content->setState(Workflow\AbstractWorkflow::STATE_PUBLISHED);
        $this->saveEntity($content, true);
    }

    public function getAllStates(Model\Document $document)
    {

    }

    public function getAvailableStates(Model\Document $document)
    {

    }

    /**
     * Sets a workflow state to the content
     * @param Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setState(Model\Content $content, $state)
    {
        $document   = $this->getContentDocument($content);
        $workflow   = $this->getWorkflow($document);
        $states     = $workflow->getAllStates();
        if (!in_array($state, $states)) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Unknown state value; Available: %s', __METHOD__, implode(', ', $states)));
        }
        //TODO - authorization
        if (true /* uzivatel ma pravo na change*/) {

        }
        if ($state == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
            $this->publishContent($content);
        } else {
            $content->setState($state);
            $this->saveEntity($content);
        }
    }

//     /**
//      * @param Model\Document $document
//      * @param int $index
//      * @return Model\Content
//      */
//     public function getPublishedContent(Model\Document $document, $index)
//     {
//         $index = $index ? $index : 0; //@todo: exception na is_int($index);
//         $contents = $this->getDocumentContents($document, $index);
//         foreach ($contents as $content) {
//             if($content->getState() == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
//                 return $content;
//             }
//         }
//
//         return null;
//     }
//
//     public function getContents(Model\Document $document, $index)
//     {
//         if(!is_integer($index)) {
//             throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
//         }
//
//         return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
//     }

    /**
     * @param Model\Entity $entity
     * @param string $name
     * @param string $data
     */
    public function saveResource(Model\Entity $entity, $name, $data)
    {
        $this->repository->saveResource($entity, $name, $data);
        $this->repository->commit();
    }

    /**
     * Returns array of published contents of given document.
     * @param Model\Document $document
     * @return Model\Content[]
     */
    public function getPublishedContents(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');
        $contents = array();
        usort($containers,
                function (Model\ContentContainer $a, Model\ContentContainer $b)
                {
                    return $a->getOrder() < $b->getOrder();
                });
        foreach ($containers as $container) {
            if ($content = $this->getPublishedContent($container)) {
                $contents[] = $content;
            }
        }
        return $contents;
    }

    /**
     * @param Model\Document $document
     * @return array <\Vivo\CMS\Model\ContentContainer>
     */
    public function getContentContainers(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');
        return $containers;
    }

    /**
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContents(Model\ContentContainer $container)
    {
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        return $contents;
    }

    /**
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|false
     * @throws Exception\LogicException when there are more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container)
    {
        $result = array();
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        foreach ($contents as $content) {
            /* @var $content Model\Content */
            if ($content->getState() == Workflow\Basic::STATE_PUBLISHED) {
                $result[] = $content;
            }
        }
        if (count($result) == 1) {
            return $result[0];
        } elseif (count($result) == 0) {
            return false;
        } else {
            throw new Exception\LogicException(
                    sprintf("%s: The ContentContainer '%s' contains more than one published content.",
                            __METHOD__, $container->getPath()));
        }
    }

    /**
     * Returns input stream for resource of entity.
     * @param Model\Entity $entity
     * @param string $resourcePath
     * @return \Vivo\IO\InputStreamInterface
     */
    public function readResource(Model\Entity $entity, $resourcePath)
    {
        return $this->repository->readResource($entity, $resourcePath);
    }

    /**
     * Returns content of entity resource.
     * @param Model\Entity $entity
     * @param string $resourcePath
     * @return string
     */
    public function getResource(Model\Entity $entity, $resourcePath)
    {
        return $this->repository->getResource($entity, $resourcePath);
    }

    public function getEntityUrl(Model\Entity $entity)
    {
        //TODO - implement using PathBuilder
        $parts = explode('/ROOT/', $entity->getPath());
        return $parts[1];
    }

    /**
     * Returns entity relative path within site
     * Relative path starts and ends with slash.
     * @param Model\Entity $entity
     * @example '/path/to/some-document-within-site/'
     * @return string
     */
    public function getEntityRelPath(Model\Entity $entity)
    {
        //TODO - implement using PathBuilder
        $parts = explode('/ROOT', $entity->getPath());
        return $parts[1];
    }

    /**
     * Returns site path of given entity.
     * @param Model\Entity $entity
     * @return string
     */
    public function getEntitySitePath(Model\Entity $entity) {
        //TODO - implement using PathBuilder
        $parts = explode('/ROOT/', $entity->getPath());
        return $parts[0];
    }

    /**
     * Returns entity within site by rel path.
     * @param string $relPath
     * @param Model\Site $site
     * @return Model\Entity
     */
    public function getSiteEntity($relPath, Model\Site $site)
    {
        $path = $this->getEntityAbsolutePath($relPath, $site);
        return $this->getEntity($path);
    }

    /**
     * Returns
     * @param string $path
     * @param Model\Site $site
     * @throws InvalidArgumentException
     * @return string
     */
    public function getEntityAbsolutePath($path, Model\Site $site)
    {
        if (substr($path, 0, 1) == '/' && substr($path, -1) == '/') {
            //it's relative path
            $components     = array($site->getPath(), 'ROOT', $path);
            $absolutePath   = $this->pathBuilder->buildStoragePath($components, true);
        } else {
            //it's absolute path
            $absolutePath   = $path;
        }
        return $absolutePath;
    }

    /**
     * Returns child documents.
     * @param Model\Document $document
     * @return array
     */
    public function getChildDocuments(Model\Document $document)
    {
        $children = $this->getChildren($document);
        $result = array();
        foreach ($children as $child) {
            if ($child instanceof Model\Document) {
                $result[] = $child;
            }
        }
        return $result;
    }

    /**
     * Returns site object by its name (ie name of the site folder in repository)
     * @param string $siteName
     * @throws Exception\DomainException
     * @return Model\Entity
     */
    public function getSite($siteName)
    {
        $path   = $this->pathBuilder->buildStoragePath(array($siteName), true);
        $site   = $this->getEntity($path);
        if (!$site instanceof \Vivo\CMS\Model\Site) {
            throw new Exception\DomainException(
                sprintf("%s: Returned object is not of '%s' type", __METHOD__, '\Vivo\CMS\Model\Site'));
        }
        return $site;
    }

    /**
     * Returns if site with the specified name exists
     * @param string $siteName
     * @return bool
     */
    public function siteExists($siteName)
    {
        try {
            $this->getSite($siteName);
            $siteExists = true;
        } catch (\Vivo\Repository\Exception\EntityNotFoundException $e) {
            $siteExists = false;
        }
        return $siteExists;
    }

    /**
     * Returns entities specified by the indexer query
     * @param QueryInterface|string $spec Either QueryInterface or a string query
     * @param QueryParams|array|null $queryParams Either a QueryParams object or an array specifying the params
     * @return \Vivo\CMS\Model\Entity[]
     */
    public function getEntitiesByQuery($spec, $queryParams = null)
    {
        if (is_string($spec)) {
            //Parse string query to Query object
            $spec   = $this->queryParser->stringToQuery($spec);
        }
        $result     = $this->indexer->find($spec, $queryParams);
        $hits       = $result->getHits();
        $entities   = array();
        /** @var $hit \Vivo\Indexer\QueryHit */
        foreach ($hits as $hit) {
            $doc    = $hit->getDocument();
            $path   = $doc->getFieldValue('\\path');
            try {
                $entity = $this->repository->getEntity($path);
                $entities[] = $entity;
            } catch (EntityNotFoundException $e) {
                //Entity not found
            }
        }
        return $entities;
    }

    /**
     * Reindex all entities (contents and children) saved under entity
     * First commits any uncommitted changes in the repository
     * Returns number of reindexed items
     * @param string $path Path to entity
     * @param bool $deep If true reindexes whole subtree
     * @throws \Exception
     * @return int
     */
    public function reindex($path, $deep = false)
    {
        //The reindexing may not rely on the indexer in any way! Presume the indexer data is corrupt.
        $this->repository->commit();
        $this->indexer->begin();
        try {
            if ($deep) {
                $delQuery   = $this->indexerHelper->buildTreeQuery($path);
            } else {
                $delQuery   = $this->qb->cond(sprintf('\path:%s', $path));
            }
            $this->indexer->delete($delQuery);
            $count      = 0;
            $entity     = $this->repository->getEntityFromStorage($path);
            if ($entity) {
                if ($entity instanceof Model\Document) {
                    $publishedContentTypes  = $this->getPublishedContentTypes($entity);
                } else {
                    $publishedContentTypes    = array();
                }
                $options    = array('published_content_types' => $publishedContentTypes);
                $idxDoc     = $this->indexerHelper->createDocument($entity, $options);
                $this->indexer->addDocument($idxDoc);
                $count      = 1;
                //TODO - reindex entity Contents
                //		if ($entity instanceof Vivo\CMS\Model\Document) {
                //            /* @var $entity \Vivo\CMS\Model\Document */
                //			for ($index = 1; $index <= $entity->getContentCount(); $index++)
                //				foreach ($entity->getContents($index) as $content)
                //					$count += $this->reindex($content, true);
                //		}
                if ($deep) {
                    $descendants    = $this->repository->getDescendantsFromStorage($path);
                    foreach ($descendants as $descendant) {
                        if ($descendant instanceof Model\Document) {
                            $publishedContentTypes  = $this->getPublishedContentTypes($descendant);
                        } else {
                            $publishedContentTypes  = array();
                        }
                        $options    = array('published_content_types' => $publishedContentTypes);
                        $idxDoc     = $this->indexerHelper->createDocument($descendant, $options);
                        $this->indexer->addDocument($idxDoc);
                        $count++;
                    }
                }
            }
            $this->indexer->commit();
        } catch (\Exception $e) {
            $this->indexer->rollback();
            throw $e;
        }
        return $count;
    }

    /**
     * Returns array of published content types (class names of published contents)
     * If there are no published contents, returns an empty array
     * @param \Vivo\CMS\Model\Document $document
     * @return string[]
     */
    protected function getPublishedContentTypes(Model\Document $document)
    {
        $publishedContents      = $this->getPublishedContents($document);
        $publishedContentTypes  = array();
        /** @var $publishedContent Model\Content */
        foreach ($publishedContents as $publishedContent) {
            $publishedContentTypes[]    = get_class($publishedContent);
        }
        return $publishedContentTypes;
    }

    /**
     * If $ident represents UUID or a symbolic reference, returns corresponding UUID, otherwise null
     * @param string $ident
     * @return null|string
     */
    protected function getUuidFromEntityIdent($ident)
    {
        if (preg_match('/^'.self::UUID_PATTERN.'$/i', $ident)) {
            //UUID
            $uuid   = $ident;
            $uuid   = strtoupper($uuid);
        } elseif (preg_match('/^\[ref:('.self::UUID_PATTERN.')\]$/i', $ident, $matches)) {
            //Symbolic reference in [ref:uuid] format
            $uuid   = $matches[1];
            $uuid = strtoupper($uuid);
        } else {
            //The ident is not a UUID
            $uuid   = null;
        }
        return $uuid;
    }

    /**
     * Returns an array of duplicate uuids under specified path, works directly on storage
     * array(
     *  'uuid1' => array(
     *      'path1',
     *      'path2',
     *  ),
     *  'uuid2' => array(
     *      'path3',
     *      'path4',
     *  ),
     * )
     * @param string $path
     * @return array
     */
    public function getDuplicateUuidsInStorage($path)
    {
        $uuids          = array();
        $descendants    = $this->repository->getDescendantsFromStorage($path);
        $me             = $this->repository->getEntityFromStorage($path);
        if ($me) {
            $descendants[]  = $me;
        }
        /** @var $descendant Model\Entity */
        foreach ($descendants as $descendant) {
            $uuid   = $descendant->getUuid();
            if (!array_key_exists($uuid, $uuids)) {
                $uuids[$uuid]   = array();
            }
            $uuids[$uuid][]  = $descendant->getPath();
        }
        foreach ($uuids as $uuid => $paths) {
            if (count($paths) == 1) {
                unset($uuids[$uuid]);
            }
        }
        return $uuids;
    }
}
