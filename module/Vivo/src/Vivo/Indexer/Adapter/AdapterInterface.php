<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Query;
use Vivo\Indexer\QueryHit;
use Vivo\TransactionalInterface;
use Vivo\Indexer\Term as IndexTerm;

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
     * Returns an array of document ids
     * @param IndexTerm $term
     * @return array
     */
    public function termDocs(IndexTerm $term);
}
