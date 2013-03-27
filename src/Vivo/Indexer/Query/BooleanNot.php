<?php
namespace Vivo\Indexer\Query;

/**
 * BooleanNot
 * Boolean NOT query
 */
class BooleanNot implements BooleanInterface
{
    /**
     * Query
     * @var QueryInterface
     */
    protected $query;

    /**
     * Construct
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query)
    {
        $this->query    = $query;
    }

    /**
     * Returns the query
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }
}
