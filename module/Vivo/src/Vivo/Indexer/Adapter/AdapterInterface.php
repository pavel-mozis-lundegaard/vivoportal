<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Query;
use Vivo\Indexer\Document;
use Vivo\Indexer\Result;
use Vivo\Indexer\QueryParams;
use Vivo\TransactionalInterface;

/**
 * AdapterInterface
 */
interface AdapterInterface extends TransactionalInterface
{
    /**
     * Finds documents matching the query in the index and returns an array of query hits
     * If there are no documents found, returns an empty array
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @param \Vivo\Indexer\QueryParams $queryParams
     * @return Result
     */
    public function find(Query\QueryInterface $query, QueryParams $queryParams);

    /**
     * Deletes documents from the index
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return void
     */
    public function delete(Query\QueryInterface $query);

    /**
     * Deletes document by its unique ID
     * @param string $docId
     */
    public function deleteById($docId);

    /**
     * Adds a document into the index
     * @param \Vivo\Indexer\Document $doc
     * @return void
     */
    public function addDocument(Document $doc);

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize();

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll();

    /**
     * Returns number of undeleted documents currently present in the index
     * @return integer
     */
    public function getDocumentCountUndeleted();

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments();
}
