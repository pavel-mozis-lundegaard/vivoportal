<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Query;
use Vivo\Indexer\QueryHit;
use Vivo\TransactionalInterface;
use Vivo\Indexer\Term as IndexTerm;
use Vivo\Indexer\Document;

/**
 * AdapterInterface
 */
interface AdapterInterface extends TransactionalInterface
{
    /**
     * Finds documents matching the query in the index and returns an array of query hits
     * If there are no documents found, returns an empty array
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query);

    /**
     * Deletes documents from the index
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return void
     */
    public function delete(Query\QueryInterface $query);

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
