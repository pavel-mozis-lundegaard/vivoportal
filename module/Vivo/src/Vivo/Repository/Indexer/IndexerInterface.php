<?php
namespace Vivo\Repository\Indexer;

use Vivo\CMS\Model;
use Vivo\TransactionalInterface;

interface IndexerInterface extends TransactionalInterface {

	public function execute(Query $query);

	public function save(Model\Entity $entity);

}

