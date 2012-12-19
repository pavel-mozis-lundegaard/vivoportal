<?php
namespace Vivo\Indexer;

use Vivo\Indexer\Query\QueryInterface;
use Vivo\Indexer\Query\BooleanAnd;
use Vivo\Indexer\Query\BooleanOr;
use Vivo\Indexer\Query\BooleanNot;
use Vivo\Indexer\Query\Range;
use Vivo\Indexer\Query\Term as TermQuery;
use Vivo\Indexer\Query\Wildcard;
use Vivo\Indexer\Term as IndexerTerm;

/**
 * QueryBuilder
 */
class QueryBuilder
{
    /**
     * Creates a query from the specified condition
     * @param string $cond
     * @param string|null $field
     * @param bool $neg
     * @throws Exception\InvalidArgumentException
     * @return QueryInterface
     */
    public function cond($cond, $field = null, $neg = false)
    {
        $reRange    = '/^\[(.+)\s+[tT][oO]\s+(.+)\]$/';
        $cond       = trim($cond);
        $matches    = array();
        if (preg_match($reRange, $cond, $matches) === 1) {
            //Range query
            if (is_null($field)) {
                throw new Exception\InvalidArgumentException(
                    sprintf('%s: Field name must be specified for range conditions', __METHOD__));
            }
            $query  = new Range($field, $matches[1], $matches[2]);
        } elseif (mb_strpos($cond, '*') !== false) {
            //Wildcard query
            $pattern    = new IndexerTerm($cond, $field);
            $query      = new Wildcard($pattern);
        } else {
            //Term query
            $term       = new IndexerTerm($cond, $field);
            $query      = new TermQuery($term);
        }
        if ($neg) {
            $query      = $this->notX($query);
        }
        return $query;
    }

    /**
     * Negates and returns a query
     * @param QueryInterface $query
     * @return BooleanNot
     */
    public function notX($query)
    {
        $query = new BooleanNot($query);
        return $query;
    }

    /**
     * Combines two queries into an AND query
     * @param QueryInterface $left
     * @param QueryInterface $right
     * @return BooleanAnd
     */
    public function andX(QueryInterface $left, QueryInterface $right)
    {
        $query = new BooleanAnd($left, $right);
        return $query;
    }

    /**
     * Combines two queries into an OR query
     * @param QueryInterface $left
     * @param QueryInterface $right
     * @return BooleanAnd
     */
    public function orX(QueryInterface $left, QueryInterface $right)
    {
        $query = new BooleanOr($left, $right);
        return $query;
    }
}
