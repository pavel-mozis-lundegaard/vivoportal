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
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query)
    {
        return array();
    }

    /**
     * Finds documents based on a term (returns docIds)
     * This is usually faster than find()
     * Returns an array of document ids, if no documents are found, returns an empty array
     * @param Term $term
     * @return array
     */
    public function termDocs(Term $term)
    {
        return array();
    }

    /**
     * Finds documents based on a term (returns document objects)
     * This is usually faster than find()
     * Returns an array of document objects, if no documents are found, returns an empty array
     * @param Term $term
     * @return Document[]
     */
    public function termDocsObj(Term $term)
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
     * Deletes documents identified by a term from the index (faster than delete())
     * @param Term $term
     */
    public function deleteByTerm(Term $term)
    {
    }

    /**
     * Returns a document by its ID
     * If the document with this ID does not exist, returns null
     * @param string $docId
     * @return null|Document
     */
    public function getDocument($docId)
    {
        return null;
    }

    /**
     * Deletes a document from the index
     * @param string $docId
     */
    public function removeDocument($docId)
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