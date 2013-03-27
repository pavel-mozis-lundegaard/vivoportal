<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;

/**
 * WildcardInterface
 * Wildcard query interface
 */
interface WildcardInterface extends QueryInterface
{
    /**
     * Returns the search pattern
     * @return IndexTerm
     */
    public function getPattern();
}