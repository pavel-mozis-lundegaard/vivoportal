<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;

/**
 * Term
 * Term query
 */
class Term implements TermInterface
{
    /**
     * Index term
     * @var IndexTerm
     */
    protected $term;

    /**
     * Constructor
     * @param IndexTerm $term
     */
    public function __construct(IndexTerm $term)
    {
        $this->setTerm($term);
    }

    /**
     * Sets the term
     * @param IndexTerm $term
     */
    public function setTerm(IndexTerm $term)
    {
        $this->term = $term;
    }

    /**
     * Returns the index term
     * @return IndexTerm
     */
    public function getTerm()
    {
        return $this->term;
    }

}