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
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query);

    /**
     * Finds documents based on a term (returns docIds)
     * This is usually faster than find()
     * Returns an array of document ids, if no documents are found, returns an empty array
     * @param Term $term
     * @return array
     */
    public function termDocs(Term $term);

    /**
     * Finds documents based on a term (returns document objects)
     * This is usually faster than find()
     * Returns an array of document objects, if no documents are found, returns an empty array
     * @param Term $term
     * @return Document[]
     */
    public function termDocsObj(Term $term);

    /**
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query);

    /**
     * Deletes documents identified by a term from the index (faster than delete())
     * @param Term $term
     */
    public function deleteByTerm(Term $term);

    /**
     * Returns a document by its ID
     * If the document with this ID does not exist, returns null
     * @param string $docId
     * @return null|Document
     */
    public function getDocument($docId);

    /**
     * Deletes a document from the index
     * @param string $docId
     */
    public function removeDocument($docId);

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

    /**
     * Adds a document into index
     * @param Document $document
     */
    public function addDocument(Document $document);
}