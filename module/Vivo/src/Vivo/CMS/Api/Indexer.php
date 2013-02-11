<?php
namespace Vivo\CMS\Api;

use Vivo\Indexer\Query\QueryInterface;
use Vivo\Indexer\QueryParams;
use Vivo\Indexer\Query\Parser\ParserInterface;
use Vivo\Indexer\IndexerInterface as VivoIndexerInterface;
use Vivo\CMS\Indexer\IndexerHelperInterface;
use Vivo\Indexer\QueryBuilder;
use Vivo\Repository\RepositoryInterface;
use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\CMS\Model;
use Vivo\CMS\Model\Site;
use Vivo\CMS\Api\DocumentInterface as DocumentApi;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Zend\EventManager\EventManagerInterface;
use Vivo\Repository\EventInterface as RepositoryEventInterface;
use Vivo\Indexer\Query\Term as TermQuery;

/**
 * Indexer
 * Indexer API
 */
class Indexer implements IndexerInterface
{
    /**
     * Query Parser
     * @var ParserInterface
     */
    protected $queryParser;

    /**
     * Vivo Indexer
     * @var VivoIndexerInterface
     */
    protected $indexer;

    /**
     * Indexer helper
     * @var IndexerHelperInterface
     */
    protected $indexerHelper;

    /**
     * Query Builder
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * Repository
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Document API
     * @var DocumentApi
     */
    protected $documentApi;

    /**
     * Path Builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Constructor
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param \Vivo\CMS\Indexer\IndexerHelperInterface $indexerHelper
     * @param \Vivo\Indexer\Query\Parser\ParserInterface $queryParser
     * @param \Vivo\Indexer\QueryBuilder $queryBuilder
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param DocumentApi $documentApi
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     * @param \Zend\EventManager\EventManagerInterface $repositoryEvents
     */
    public function __construct(VivoIndexerInterface $indexer,
                                IndexerHelperInterface $indexerHelper,
                                ParserInterface $queryParser,
                                QueryBuilder $queryBuilder,
                                RepositoryInterface $repository,
                                DocumentApi $documentApi,
                                PathBuilderInterface $pathBuilder,
                                EventManagerInterface $repositoryEvents = null)
    {
        $this->indexer          = $indexer;
        $this->indexerHelper    = $indexerHelper;
        $this->queryParser      = $queryParser;
        $this->qb               = $queryBuilder;
        $this->repository       = $repository;
        $this->documentApi      = $documentApi;
        $this->pathBuilder      = $pathBuilder;
        if ($repositoryEvents) {
            $repositoryEvents->attach(RepositoryEventInterface::EVENT_COMMIT, array($this, 'onRepositoryCommit'));
        }
    }

    /**
     * Returns entities specified by the indexer query
     * @param QueryInterface|string $spec Either QueryInterface or a string query
     * @param QueryParams|array|null $queryParams Either a QueryParams object or an array specifying the params
     * @return Model\Entity[]
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
     * Reindex all entities (contents and children) saved under the given path
     * Returns number of reindexed items
     * @param \Vivo\CMS\Model\Site $site
     * @param string $path Path to entity within the site
     * @param bool $deep If true reindexes whole subtree
     * @throws \Exception
     * @return int
     */
    public function reindex(Site $site, $path = '/', $deep = false)
    {
        //The reindexing may not rely on the indexer in any way! Presume the indexer data is corrupt.
        $pathComponents = array($site->getPath(), $path);
        $path           = $this->pathBuilder->buildStoragePath($pathComponents, true);
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
                $this->indexEntity($entity);
                $count  = 1;
                if ($deep) {
                    $descendants    = $this->repository->getDescendantsFromStorage($path);
                    foreach ($descendants as $descendant) {
                        $this->indexEntity($descendant);
                    }
                    $count  += count($descendants);
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
     * Adds entity into index
     * @param \Vivo\CMS\Model\Entity $entity
     */
    protected function indexEntity(Model\Entity $entity)
    {
        if ($entity instanceof Model\Document) {
            $publishedContentTypes  = $this->documentApi->getPublishedContentTypes($entity);
        } else {
            $publishedContentTypes    = array();
        }
        $options    = array('published_content_types' => $publishedContentTypes);
        $idxDoc     = $this->indexerHelper->createDocument($entity, $options);
        $this->indexer->addDocument($idxDoc);
        //TODO - reindex entity Contents
        //		if ($entity instanceof Vivo\CMS\Model\Document) {
        //            /* @var $entity \Vivo\CMS\Model\Document */
        //			for ($index = 1; $index <= $entity->getContentCount(); $index++)
        //				foreach ($entity->getContents($index) as $content)
        //					$count += $this->reindex($content, true);
        //		}
    }

    /**
     * Removes entity and whole its subtree from index
     * @param Model\Entity|string $spec Either an Entity object or its path
     */
    protected function deleteEntity($spec)
    {
        $delQuery   = $this->indexerHelper->buildTreeQuery($spec);
        $this->indexer->delete($delQuery);
    }

    /**
     * Saves or updated entity in index
     * @param \Vivo\CMS\Model\Entity $entity
     */
    protected function saveEntity(Model\Entity $entity)
    {
        //Remove old doc
        $entityTerm = $this->indexerHelper->buildEntityTerm($entity);
        $delQuery   = new TermQuery($entityTerm);
        $this->indexer->delete($delQuery);
        //Insert new one
        $this->indexEntity($entity);
    }

    /**
     * Repository commit listener
     * @param \Vivo\Repository\EventInterface $event
     */
    public function onRepositoryCommit(RepositoryEventInterface $event)
    {
        $this->indexer->begin();
        /** @var $deleteEntityPaths array */
        $deleteEntityPaths  = $event->getParam('delete_entity_paths');
        foreach ($deleteEntityPaths as $deleteEntityPath) {
            $this->deleteEntity($deleteEntityPath);
        }
        /** @var $saveEntities Model\Entity[] */
        $saveEntities   = $event->getParam('save_entities');
        foreach ($saveEntities as $entity) {
            $this->saveEntity($entity);
        }


        //TODO - implement also support for other repository actions with resources etc.


        $this->indexer->commit();
    }
}
