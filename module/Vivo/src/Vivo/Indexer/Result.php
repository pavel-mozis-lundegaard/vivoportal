<?php
namespace Vivo\Indexer;

use IteratorAggregate;
use Traversable;
use ArrayObject;

/**
 * Result
 */
class Result implements IteratorAggregate
{
    /**
     * Query hits (hits actually contained in this result)
     * @var QueryHit[]
     */
    protected $hits;

    /**
     * Number of all hits the query would produce
     * @var integer
     */
    protected $totalHits;

    /**
     * Parameters the query was run with
     * @var QueryParams
     */
    protected $queryParams;

    /**
     * Constructor
     * @param QueryHit[] $hits
     * @param integer $totalHits
     * @param QueryParams $queryParams
     */
    public function __construct(array $hits, $totalHits, QueryParams $queryParams)
    {
        $this->hits         = $hits;
        $this->totalHits    = $totalHits;
        $this->queryParams  = $queryParams;
    }

    /**
     * Returns number of all hits the query would produce
     * @return int
     */
    public function getTotalHitCount()
    {
        return $this->totalHits;
    }

    /**
     * Returns number of hits actually contained in this result
     * @return int
     */
    public function getCount()
    {
        return count($this->hits);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * IteratorAggregate implementation function. Allows usage:
     * <code>
     * foreach ($result as $queryHit)
     * {
     * 	...
     * }
     * </code>
     */
    public function getIterator()
    {
        $arrayObject = new ArrayObject($this->hits);
        return $arrayObject->getIterator();
    }

    /**
     * Returns parameters the query was run with
     * @return QueryParams
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
}
