<?php
namespace Vivo\Indexer;

use Vivo\TransactionalInterface;

/**
 * Indexer
 */
class Indexer implements TransactionalInterface
{
    /**
     * Indexer adapter
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Construct
     * @param Adapter\AdapterInterface $adapter
     */
    public function __construct(Adapter\AdapterInterface $adapter)
    {
        $this->adapter  = $adapter;
    }

    /**
     * Returns an array of hits
     * @param Query\QueryInterface $query
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query)
	{
        return $this->adapter->find($query);
	}
    /**
     * Finds documents based on a term (returns docIds)
     * This is usually faster than find()
     * Returns an array of document ids, if no documents are found, returns an empty array
     * @param Term $term
     * @return array
     */
    public function termDocs(Term $term) {
        return $this->adapter->termDocs($term);
    }

    /**
     * Finds documents based on a term (returns document objects)
     * This is usually faster than find()
     * Returns an array of document objects, if no documents are found, returns an empty array
     * @param Term $term
     * @return Document[]
     */
    public function termDocsObj(Term $term) {
        $docIds = $this->termDocs($term);
        $docs   = array();
        if (count($docIds) > 0) {
            foreach ($docIds as $docId) {
                $doc    = $this->getDocument($docId);
                $docs[] = $doc;
            }
        }
        return $docs;
    }

    /**
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query)
    {
        $hits   = $this->find($query);
        foreach ($hits as $hit) {
            $this->removeDocument($hit->getDocId());
        }
    }

    /**
     * Deletes documents identified by a term from the index (faster than delete())
     * @param Term $term
     */
    public function deleteByTerm(Term $term) {
        $docIds = $this->termDocs($term);
        foreach ($docIds as $docId) {
            $this->removeDocument(($docId));
        }
    }

    /**
     * Returns a document by its ID
     * If the document with this ID does not exist, returns null
     * @param string $docId
     * @return null|Document
     */
    public function getDocument($docId)
    {
        return $this->adapter->getDocument($docId);
    }

    /**
     * Deletes a document from the index
     * If deletion is ok, returns true, otherwise false
     * @param string $docId
     */
    public function removeDocument($docId)
    {
        $this->adapter->deleteDocument($docId);
    }

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize()
    {
        $this->adapter->optimize();
    }

    /**
     * Commits pending changes and starts a new transaction
     */
    public function begin()
    {
        $this->adapter->begin();
    }

    /**
     * Commits pending changes and closes the transaction
     */
    public function commit()
    {
        $this->adapter->commit();
    }

    /**
     * Rolls back any scheduled changes and closes the transaction
     */
    public function rollback()
    {
        $this->adapter->rollback();
    }

}
