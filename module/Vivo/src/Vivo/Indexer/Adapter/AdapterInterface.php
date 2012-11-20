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
     * Finds documents based on a term
     * This is usually faster than find()
     * Returns an array of document ids, if no documents are found, returns an empty array
     * @param IndexTerm $term
     * @return array
     */
    public function termDocs(IndexTerm $term);

    /**
     * Returns a document by its ID
     * If the document with this ID does not exist, returns null
     * @param string $docId
     * @return Document|null
     */
    public function getDocument($docId);

    /**
     * Deletes a document from the index
     * @param string $docId
     * @return void
     */
    public function deleteDocument($docId);

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
