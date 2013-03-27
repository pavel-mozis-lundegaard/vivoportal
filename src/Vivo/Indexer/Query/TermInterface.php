<?php
namespace Vivo\Indexer\Query;

use Vivo\Indexer\Term as IndexTerm;

/**
 * TermInterface
 * Term query interface
 */
interface TermInterface extends QueryInterface
{
    /**
     * Returns the index term from the query
     * @return IndexTerm
     */
    public function getTerm();
}