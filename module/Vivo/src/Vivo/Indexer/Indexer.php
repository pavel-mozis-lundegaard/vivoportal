<?php
namespace Vivo\Indexer;

/**
 * Indexer
 */
class Indexer
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
     * Finds documents based on a term
     * This is usually faster than find()
     * Returns an array of document ids
     * @param Term $term
     * @return array
     */
    public function termDocs(Term $term) {
        return $this->adapter->termDocs($term);
    }

    public function delete(Query\QueryInterface $query)
    {

    }

    public function getDoc($docId)
    {

    }

    public function removeDoc($docId)
    {

    }

}
