<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;
use Vivo\Indexer\Exception;

/**
 * MultiTerm
 * Multi-term query
 */
class MultiTerm implements MultiTermInterface
{
    /**
     * Indexed array of terms
     * @var IndexTerm[]
     */
    protected $terms    = array();

    /**
     * Indexed array containing signs for the terms
     * true = required, false = prohibited
     * @var boolean[]
     */
    protected $signs    = array();

    /**
     * Constructor
     * @param array $terms
     * @param array $signs
     * @throws \Vivo\Indexer\Exception\InvalidArgumentException
     */
    public function __construct(array $terms = array(), array $signs = array())
    {
        if (count($terms) != count($signs)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Term count (%s) does not match sign count (%s)",
                                __METHOD__, count($terms),  count($signs)));
        }
        $this->terms    = $terms;
        $this->signs    = $signs;
    }

    /**
     * Adds a term into query
     * @param \Vivo\Indexer\Term $term
     * @param boolean $sign true = required, false = prohibited
     */
    public function addTerm(IndexTerm $term, $sign = true) {
        $sign           = (bool)$sign;
        $this->terms[]  = $term;
        $this->signs[]  = $sign;
    }

    /**
     * Returns query terms in an indexed array
     * @return \Vivo\Indexer\Term[]
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Returns term signs in an indexed array
     * @return boolean[]
     */
    public function getSigns()
    {
        return $this->signs;
    }
}