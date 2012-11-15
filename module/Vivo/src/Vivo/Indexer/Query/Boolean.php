<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Exception;

/**
 * Boolean
 * Boolean query
 */
class Boolean implements BooleanInterface
{
    /**
     * Array of subqueries
     * @var QueryInterface[]
     */
    protected $subqueries   = array();

    /**
     * Array of subquery signs
     * true = required, false = prohibited, null = neither
     * @var boolean[]
     */
    protected $signs        = array();

    /**
     * Constructor
     * @param array $subqueries
     * @param array $signs
     * @throws \Vivo\Indexer\Exception\InvalidArgumentException
     */
    public function __construct(array $subqueries = array(), array $signs = array())
    {
        if (count($subqueries) != count($signs)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Subquery count (%s) does not match sign count (%s)",
                __METHOD__, count($subqueries),  count($signs)));
        }
        $this->subqueries   = $subqueries;
        $this->signs        = $signs;
    }

    /**
     * Adds a subquery
     * @param QueryInterface $subquery
     * @param boolean|null $sign true = required, false = prohibited, null = neither
     */
    public function addSubquery(QueryInterface $subquery, $sign = null)
    {
        if (!is_null($sign)) {
            $sign   = (bool)$sign;
        }
        $this->subqueries[] = $subquery;
        $this->signs[]      = $sign;
    }

    /**
     * Returns subqueries
     * @return QueryInterface[]
     */
    public function getSubqueries()
    {
        return $this->subqueries;
    }

    /**
     * Return subquery signs
     * @return boolean[]
     */
    public function getSigns()
    {
        return $this->signs;
    }
}