<?php
namespace Vivo\Indexer\Query;

/**
 * BooleanTwoOpInterface
 * Boolean interface for two operands
 */
interface BooleanTwoOpInterface extends BooleanInterface
{
    /**
     * Returns the query on the left side of the AND operand
     * @return QueryInterface
     */
    public function getQueryLeft();

    /**
     * Returns the query on the right side of the AND operand
     * @return QueryInterface
     */
    public function getQueryRight();
}
