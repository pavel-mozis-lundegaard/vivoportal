<?php
namespace Vivo\Repository\Indexer;

namespace Vivo\CMS\Model;

interface IndexerInterface extends TransactionalInterface {

    const HYDRATE_OBJECT = 'OBJECT';
    /**
     * Hydrates an array graph.
     */
    const HYDRATE_ARRAY = 'ARRAY';
    /**
     * Hydrates a flat, rectangular result set with scalar values.
     */
    const HYDRATE_SCALAR = 'SCALAR';
    /**
     * Hydrates a single scalar value.
     */
//     const HYDRATE_SINGLE_SCALAR = 'SINGLE_SCALAR';

    /**
     * Very simple object hydrator (optimized for performance).
     */
//     const HYDRATE_SIMPLEOBJECT = 5;


	public function execute(Query $query, $hydratationMode = self::HYDRATE_ARRAY);

	public function save(Model\Entity $entity);

}
