<?php
namespace Vivo\Indexer;

use Vivo\TransactionalInterface;

/**
 * IndexerInterface
 */
interface IndexerInterface extends  TransactionalInterface
{
    /**
     * Returns an array of hits
     * @param Query\QueryInterface $query
     * @param QueryParams $queryParams
     * @return Result
     */
    public function find(Query\QueryInterface $query, QueryParams $queryParams);

    /**
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query);

    /**
     * Adds a document into index
     * @param Document $document
     */
    public function addDocument(Document $document);

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize();

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments();

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll();

    /**
     * Returns number of undeleted document in the index
     * @return int
     */
    public function getDocumentCountUndeleted();

    /**
     * Returns number of deleted documents in the index
     * @return int
     */
    public function getDocumentCountDeleted();
}