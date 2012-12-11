<?php
namespace Vivo\Indexer;

use Vivo\Indexer\Document;

/**
 * Indexer
 */
class Indexer implements IndexerInterface
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
     * Deletes documents identified by a query from the index
     * @param Query\QueryInterface $query
     */
    public function delete(Query\QueryInterface $query)
    {
        $this->adapter->delete($query);
    }

    /**
     * Adds a document into index
     * @param Document $document
     */
    public function addDocument(Document $document)
    {
        $this->adapter->addDocument($document);
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

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments()
    {
        $this->adapter->deleteAllDocuments();
    }

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll()
    {
        return $this->adapter->getDocumentCountAll();
    }

    /**
     * Returns number of undeleted document in the index
     * @return int
     */
    public function getDocumentCountUndeleted()
    {
        return $this->adapter->getDocumentCountUndeleted();
    }

    /**
     * Returns number of deleted documents in the index
     * @return int
     */
    public function getDocumentCountDeleted()
    {
        $deletedCount   = $this->getDocumentCountAll() - $this->getDocumentCountUndeleted();
        return $deletedCount;
    }
}
