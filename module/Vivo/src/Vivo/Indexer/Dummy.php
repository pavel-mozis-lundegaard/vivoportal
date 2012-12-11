<?php
namespace Vivo\Indexer;

/**
 * Dummy
 * Dummy indexer - does nothing
 */
class Dummy implements IndexerInterface
{

    /**
     * Returns an array of hits
     * @param Query\QueryInterface $query
     * @param QueryParams $queryParams
     * @return Result
     */
    public function find(Query\QueryInterface $query, QueryParams $queryParams)
    {
        return array();
    }

    /**
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query)
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

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll()
    {
        return 0;
    }

    /**
     * Returns number of undeleted document in the index
     * @return int
     */
    public function getDocumentCountUndeleted()
    {
        return 0;
    }

    /**
     * Returns number of deleted documents in the index
     * @return int
     */
    public function getDocumentCountDeleted()
    {
        return 0;
    }

    /**
     * Adds a document into index
     * @param Document $document
     */
    public function addDocument(Document $document)
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