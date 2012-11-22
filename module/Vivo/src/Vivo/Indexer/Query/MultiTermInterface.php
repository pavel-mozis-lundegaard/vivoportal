<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;

/**
 * MultiTermInterface
 * Multi-term query interface
 */
interface MultiTermInterface extends QueryInterface
{
    /**
     * Adds a term into query
     * @param IndexTerm $term
     * @param boolean|null $sign true = required, false = prohibited, null = neither required nor prohibited
     */
    public function addTerm(IndexTerm $term, $sign = null);

    /**
     * Returns query terms in an indexed array
     * @return IndexTerm[]
     */
    public function getTerms();

    /**
     * Returns term signs in an indexed array
     * @return boolean[]
     */
    public function getSigns();

}