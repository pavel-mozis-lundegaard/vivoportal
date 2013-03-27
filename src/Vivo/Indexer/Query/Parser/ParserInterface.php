<?php
namespace Vivo\Indexer\Query\Parser;

use Vivo\Indexer\Query\QueryInterface;

/**
 * ParserInterface
 */
interface ParserInterface
{
    /**
     * Unserializes string representation of query into the Query object
     * @param $string
     * @return QueryInterface
     */
    public function stringToQuery($string);

    /**
     * Serializes Query object to a string
     * @param QueryInterface $query
     * @return string
     */
    public function queryToString(QueryInterface $query);
}