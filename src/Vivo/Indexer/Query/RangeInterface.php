<?php
namespace Vivo\Indexer\Query;

/**
 * RangeInterface
 * Interface for range queries
 */
interface RangeInterface extends QueryInterface
{
    /**
     * Returns field name
     * @return string
     */
    public function getField();

    /**
     * Returns lower limit value or null if lower limit is not specified
     * @return mixed|null
     */
    public function getLowerLimit();

    /**
     * Returns if the lower limit is inclusive
     * @return boolean
     */
    public function isLowerLimitInclusive();

    /**
     * Returns upper limit value or null if upper limit is not specified
     * @return mixed|null
     */
    public function getUpperLimit();

    /**
     * Returns if the upper limit is inclusive
     * @return boolean
     */
    public function isUpperLimitInclusive();
}