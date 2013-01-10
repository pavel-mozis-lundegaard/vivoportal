<?php
namespace Vivo\Indexer\Query;

/**
 * Range
 * Range query implementation
 * Note: Older Solr versions do not support mixed ranges
 * (containing inclusive and exclusive boundaries at the same time)
 * @see: https://issues.apache.org/jira/browse/SOLR-355
 */
class Range implements RangeInterface
{
    /**
     * Name of field
     * @var string
     */
    protected $field;

    /**
     * Lower limit
     * @var mixed
     */
    protected $lowerLimit;

    /**
     * Is lower limit inclusive
     * @var bool
     */
    protected $lowerLimitInclusive      = true;

    /**
     * Upper limit
     * @var mixed
     */
    protected $upperLimit;

    /**
     * Is upper limit inclusive
     * @var bool
     */
    protected $upperLimitInclusive      = true;

    /**
     * Constructor
     * @param string $field
     * @param mixed|null $lowerLimit
     * @param mixed|null $upperLimit
     * @param bool $lowerLimitInclusive
     * @param bool $upperLimitInclusive
     */
    public function __construct($field,
                                $lowerLimit = null,
                                $upperLimit = null,
                                $lowerLimitInclusive = true,
                                $upperLimitInclusive = true)
    {
        $this->field                = $field;
        $this->lowerLimit           = $lowerLimit;
        $this->lowerLimitInclusive  = (bool)$lowerLimitInclusive;
        $this->upperLimit           = $upperLimit;
        $this->upperLimitInclusive  = (bool)$upperLimitInclusive;
    }

    /**
     * Returns field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns lower limit value or null if lower limit is not specified
     * @return mixed|null
     */
    public function getLowerLimit()
    {
        return $this->lowerLimit;
    }

    /**
     * Returns if the lower limit is inclusive
     * @return boolean
     */
    public function isLowerLimitInclusive()
    {
        return $this->lowerLimitInclusive;
    }

    /**
     * Returns upper limit value or null if upper limit is not specified
     * @return mixed|null
     */
    public function getUpperLimit()
    {
        return $this->upperLimit;
    }

    /**
     * Returns if the upper limit is inclusive
     * @return boolean
     */
    public function isUpperLimitInclusive()
    {
        return $this->upperLimitInclusive;
    }
}