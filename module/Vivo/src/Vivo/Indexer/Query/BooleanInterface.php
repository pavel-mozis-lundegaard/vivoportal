<?php
namespace Vivo\Indexer\Query;

/**
 * BooleanInterface
 * Boolean query interface
 */
interface BooleanInterface extends QueryInterface
{
    /**
     * Adds a subquery
     * @param QueryInterface $subquery
     * @param boolean|null $sign true = required, false = prohibited, null = neither
     */
    public function addSubquery(QueryInterface $subquery, $sign = null);

    /**
     * Returns subqueries
     * @return QueryInterface[]
     */
    public function getSubqueries();

    /**
     * Return subquery signs
     * @return boolean[]
     */
    public function getSigns();
}