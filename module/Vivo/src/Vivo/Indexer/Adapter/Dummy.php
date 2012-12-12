<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Result;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Document;
use Vivo\Indexer\QueryParams;
use Vivo\Indexer\Query;
use Vivo\Indexer\Field;

/**
 * Dummy
 * Dummy indexer adapter - does nothing
 */
class Dummy implements AdapterInterface
{
    /**
     * Finds documents matching the query in the index and returns a search result
     * If there are no documents found, returns an empty Result
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @param \Vivo\Indexer\QueryParams $queryParams
     * @return Result
     */
    public function find(Query\QueryInterface $query, QueryParams $queryParams)
    {
        $result     = new Result(0, 0, $queryParams);
        return $result;
    }


    /**
     * Finds and returns a document by its ID
     * If the document is not found, returns null
     * @param string $docId
     * @return Document|null
     */
    public function findById($docId)
    {
        return null;
    }

    /**
     * Deletes documents from the index
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return void
     */
    public function delete(Query\QueryInterface $query)
    {
    }

    /**
     * Deletes document by its unique ID
     * @param string $docId
     */
    public function deleteById($docId)
    {
    }

    /**
     * Adds a document into the index
     * @param \Vivo\Indexer\Document $doc
     * @return void
     */
    public function addDocument(Document $doc)
    {
    }

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize()
    {
    }

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments()
    {
    }

    public function begin()
    {
    }

    public function commit()
    {
    }

    public function rollback()
    {
    }
}
