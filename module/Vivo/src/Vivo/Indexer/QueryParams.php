<?php
namespace Vivo\Indexer;

class QueryParams
{
    /**
     * Result page size
     * @var int
     */
    protected $pageSize     = 10;

    /**
     * Result start offset
     * @var int
     */
    protected $startOffset  = 0;

    /**
     * Returns (max) number of rows the query should return
     * Enables paginating within the results
     * @return integer
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Returns row number (starting from 0) where the result set should begin
     * Enables paginating within the results
     * @return integer
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Sets the result page size
     * @param int $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Sets the result start offset
     * @param int $startOffset
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;
    }
}