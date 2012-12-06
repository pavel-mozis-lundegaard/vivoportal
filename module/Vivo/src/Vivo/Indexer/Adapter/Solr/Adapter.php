<?php
namespace Vivo\Indexer\Adapter\Solr;

use Vivo\Indexer\Adapter\AdapterInterface;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexTerm;
use Vivo\Indexer\Document;

/**
 * Adapter
 * Solr adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * Finds documents matching the query in the index and returns an array of query hits
     * If there are no documents found, returns an empty array
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query)
    {
        // TODO: Implement find() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Finds documents based on a term
     * This is usually faster than find()
     * Returns an array of document ids, if no documents are found, returns an empty array
     * @param IndexTerm $term
     * @return array
     */
    public function termDocs(IndexTerm $term)
    {
        // TODO: Implement termDocs() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Returns a document by its ID
     * If the document with this ID does not exist, returns null
     * @param string $docId
     * @return Document|null
     */
    public function getDocument($docId)
    {
        // TODO: Implement getDocument() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Deletes a document from the index
     * @param string $docId
     * @return void
     */
    public function deleteDocument($docId)
    {
        // TODO: Implement deleteDocument() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Adds a document into the index
     * @param \Vivo\Indexer\Document $doc
     * @return void
     */
    public function addDocument(Document $doc)
    {
        // TODO: Implement addDocument() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize()
    {
        // TODO: Implement optimize() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll()
    {
        // TODO: Implement getDocumentCountAll() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Returns number of undeleted documents currently present in the index
     * @return integer
     */
    public function getDocumentCountUndeleted()
    {
        // TODO: Implement getDocumentCountUndeleted() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments()
    {
        // TODO: Implement deleteAllDocuments() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function begin()
    {
        // TODO: Implement begin() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function commit()
    {
        // TODO: Implement commit() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function rollback()
    {
        // TODO: Implement rollback() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }
}
