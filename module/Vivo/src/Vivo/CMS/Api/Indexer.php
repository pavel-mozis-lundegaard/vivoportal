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
use Vivo\CMS\Api\DocumentInterface as DocumentApi;

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
     * Constructor
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param \Vivo\CMS\Indexer\IndexerHelperInterface $indexerHelper
     * @param \Vivo\Indexer\Query\Parser\ParserInterface $queryParser
     * @param \Vivo\Indexer\QueryBuilder $queryBuilder
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param DocumentApi $documentApi
     */
    public function __construct(VivoIndexerInterface $indexer, IndexerHelperInterface $indexerHelper,
                                ParserInterface $queryParser, QueryBuilder $queryBuilder,
                                RepositoryInterface $repository,
                                DocumentApi $documentApi)
    {
        $this->indexer          = $indexer;
        $this->indexerHelper    = $indexerHelper;
        $this->queryParser      = $queryParser;
        $this->qb               = $queryBuilder;
        $this->repository       = $repository;
        $this->documentApi      = $documentApi;
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
                    $publishedContentTypes  = $this->documentApi->getPublishedContentTypes($entity);
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
                            $publishedContentTypes  = $this->documentApi->getPublishedContentTypes($descendant);
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

    //TODO - is it ok to expose this method in API? (forces index out of sync with repo) - move to indexer helper?
    public function delete()
    {
//        $delQuery   = $this->indexerHelper->buildTreeQuery($entity);
//        $this->indexer->delete($delQuery);
    }

    //TODO - is it ok to expose this method in API? (forces index out of sync with repo) - move to indexer helper?
    public function save()
    {
        //Indexer - remove old doc & insert new one
//        $entityTerm = $this->indexerHelper->buildEntityTerm($entity);
//        $delQuery   = new TermQuery($entityTerm);
//        $entityDoc  = $this->indexerHelper->createDocument($entity, $entityOptions);
//        $this->indexer->delete($delQuery);
//        $this->indexer->addDocument($entityDoc);

    }

}