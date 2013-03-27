<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;

/**
 * Wildcard
 * Wildcard query
 */
class Wildcard implements WildcardInterface
{
    /**
     * Search pattern.
     * Field has to be fully specified or has to be null
     * Text may contain '*' or '?' symbols
     * @var IndexTerm
     */
    protected $pattern;

    /**
     * Constructor
     * @param IndexTerm $pattern
     */
    public function __construct(IndexTerm $pattern)
    {
        $this->pattern  = $pattern;
    }

    /**
     * Returns the search pattern
     * @return IndexTerm
     */
    public function getPattern()
    {
        return $this->pattern;
    }
}