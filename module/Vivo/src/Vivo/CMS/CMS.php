<?php
namespace Vivo\CMS;

use Vivo\CMS\Model;
use Vivo\CMS\Workflow;
use Vivo\CMS\Exception;
use Vivo\Repository\Repository;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\MultiTerm as MultiTermQuery;

use Zend\Config;

/**
 * Main business class for interact with CMS.
 */
class CMS
{
    /**
     * @var \Vivo\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Returns Site matching given hostname.
     * If no site matches the hostname, returns null
     * @param string $host
     * @return Model\Site|null
     */
    public function getSiteByHost($host)
    {
        $termHost   = new IndexerTerm('###host###/' . $host);
        $termType   = new IndexerTerm('Vivo\CMS\Model\Site', 'type');
        $query      = new MultiTermQuery();
        $query->addTerm($termHost, true);
        $query->addTerm($termType,  true);
        $entities   = $this->repository->getEntities($query);
        if (count($entities) > 0) {
            $site   = $entities[0];
        } else {
            $site   = null;
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
        $site = new Model\Site("/$name");
        $site->setDomain($domain);
        $site->setHosts($hosts);

        $config = "[config]\nvalue = \"data\"\n";

        $root = new Model\Document("/$name/ROOT");
        $root->setTitle('Home');
        $root->setWorkflow('Vivo\CMS\Workflow\Basic');

        $this->repository->saveEntity($site);
        $this->repository->saveResource($site, 'config.ini', $config);
        $this->repository->saveEntity($root);
        $this->repository->commit();

        return $site;
    }

    /**
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
     * @param string $path Relative document path in site.
     * @param Model\Site $site
     * @return Model\Document
     */
    public function getSiteDocument($path, Model\Site $site)
    {
        return $this->repository->getEntity($site->getPath() . '/ROOT/' . $path);
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
     * @param string $ident
     * @return Model\Entity
     */
    public function getEntity($ident)
    {
        return $this->repository->getEntity($ident);
    }

    /**
     * @param Model\Folder $folder
     * @return array
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
     * @param Model\Entity $entity
     */
    protected function saveEntity(Model\Entity $entity)
    {
        $this->repository->saveEntity($entity);
        $this->repository->commit();
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
        $this->repository->saveEntity($document);
        $this->repository->commit();
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
     * @param Model\Content $content
     * @return Model\Document
     */
    public function getContentDocument(Model\Content $content)
    {
        $path = $content->getPath();
        $path = substr($path, 0, strrpos($path, '/') - 1);
        $path = substr($path, 0, strrpos($path, '/'));

        $document = $this->repository->getEntity($path);

        if ($document instanceof Model\Document) {
            return $document;
        }

        //@todo: nebo exception
        return null;
    }

    public function addDocumentContent(Model\Document $document,
            Model\Content $content, $index = 0)
    {
        $path = $document->getPath();

        $version = count($this->getDocumentContents($document, $index));
        $contentPath = $path . "/Contents.$index/$version";
        $content->setPath($contentPath);
        $content->setState(Workflow\AbstractWorkflow::STATE_NEW);

        $this->repository->saveEntity($content);
        $this->repository->commit();
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @param int $version
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return Model\Content
     */
    public function getDocumentContent(Model\Document $document, $index,
            $version/*, $state {PUBLISHED}*/)
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

        $path = $document->getPath() . '/Contents.' . $index . '/' . $version;

        return $this->repository->getEntity($path);
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

        $path = $document->getPath() . '/Contents.' . $index;

        return $this->repository->getChildren(new Model\Entity($path));
    }

    /**
     * @param Model\Content $content
     */
    public function publishContent(Model\Content $content)
    {
        $document = $this->getContentDocument($content);
        $oldConent = $this
                ->getPublishedContent($document, $content->getIndex());

        if ($oldConent) {
            $oldConent->setState(Workflow\AbstractWorkflow::STATE_ARCHIVED);
            $this->repository->saveEntity($oldConent);
        }

        $content->setState(Workflow\AbstractWorkflow::STATE_PUBLISHED);
        $this->repository->saveEntity($content);
        $this->repository->commit();
    }

    public function getAllStates(Model\Document $document)
    {

    }

    public function getAvailableStates(Model\Document $document)
    {

    }

    /**
     * Nasetuje "libovolny" workflow stav obsahu.
     * @param Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setState(Model\Content $content, $state)
    {
        $document = $this->getContentDocument($content);
        $workflow = $this->getWorkflow($document);
        $states = $workflow->getAllStates();

        if (!in_array($state, $states)) {
            throw new Exception\InvalidArgumentException(
                    'Unknow state value. Available: ' . implode(', ', $states));
        }

        if (true /* uzivatel ma pravo na change*/) {

        }

        if ($state == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
            $this->publishContent($content);
        } else {
            $content->setState($state);
            $this->repository->saveEntity($content);
            $this->repository->commit();
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
     * @param Document $document
     * @return Content[]
     */
    public function getPublishedContents(Model\Document $document)
    {
        $containers = $this->repository
                ->getChildren($document, 'Vivo\CMS\Model\ContentContainer');
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
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|false
     * @throws Exception\LogicException when there are more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container)
    {
        $result = array();
        $contents = $this->repository
                ->getChildren($container, 'Vivo\CMS\Model\Content');
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
                    sprintf(
                            "%s: The ContentContainer '%s' contains more than one published content.",
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
        //TODO
        throw new \Exception('Not implemented');
    }
}
