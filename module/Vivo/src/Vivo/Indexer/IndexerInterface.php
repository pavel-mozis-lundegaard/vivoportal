<?php
namespace Vivo\Indexer;

use Vivo\TransactionalInterface;

/**
 * IndexerInterface
 */
interface IndexerInterface extends  TransactionalInterface
{
    /**
     * Returns a search result
     * @param Query\QueryInterface $query
     * @param QueryParams $queryParams
     * @return Result
     */
    public function find(Query\QueryInterface $query, QueryParams $queryParams);

    /**
     * Finds and returns a document by its ID
     * If the document is not found, returns null
     * @param string $docId
     * @return Document|null
     */
    public function findById($docId);

    /**
     * Adds a document into index
     * @param Document $document
     */
    public function addDocument(Document $document);

    /**
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query);

    /**
     * Deletes document by its unique ID
     * @param string $docId
     */
    public function deleteById($docId);

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments();

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize();
}