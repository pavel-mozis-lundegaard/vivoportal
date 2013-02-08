<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model\Site;
use Vivo\CMS\Exception\InvalidArgumentException;
use Vivo\CMS\Model;
use Vivo\CMS\Workflow;
use Vivo\CMS\Exception;
use Vivo\CMS\UuidConvertor\UuidConvertorInterface;
use Vivo\Repository\RepositoryInterface;
use Vivo\Indexer\QueryBuilder;
use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\Uuid\GeneratorInterface as UuidGeneratorInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;

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
     * Indexer API
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Construct
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param \Vivo\Indexer\QueryBuilder $qb
     * @param \Vivo\CMS\UuidConvertor\UuidConvertorInterface $uuidConvertor
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     * @param IndexerInterface $indexerApi
     */
    public function __construct(RepositoryInterface $repository,
                                QueryBuilder $qb,
                                UuidConvertorInterface $uuidConvertor,
                                UuidGeneratorInterface $uuidGenerator,
                                PathBuilderInterface $pathBuilder,
                                IndexerApiInterface $indexerApi)
    {
        $this->repository       = $repository;
        $this->qb               = $qb;
        $this->uuidConvertor    = $uuidConvertor;
        $this->uuidGenerator    = $uuidGenerator;
        $this->pathBuilder      = $pathBuilder;
        $this->indexerApi       = $indexerApi;
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
        $entities   = $this->indexerApi->getEntitiesByQuery($query, array('page_size' => 1));
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
     * Returns entity within site by rel path.
     * @param string $relPath
     * @param Model\Site $site
     * @return Model\Entity
     */
    public function getSiteEntity($relPath, Model\Site $site)
    {
        $path   = $this->getEntityAbsolutePath($relPath, $site);
        $entity = $this->repository->getEntity($path);
        return $entity;
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

    private function removeEntity(Model\Entity $entity)
    {
        $this->repository->deleteEntity($entity);
        $this->repository->commit();
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
