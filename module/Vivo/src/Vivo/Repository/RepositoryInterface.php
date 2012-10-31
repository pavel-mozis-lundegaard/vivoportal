<?php
namespace Vivo\Repository;

use Vivo\TransactionalInterface;
use Vivo\CMS\Model;

interface RepositoryInterface extends TransactionalInterface {

	/**
	 * @param string $ident
	 * @return Vivo\CMS\Model\Entity
	 */
	public function getEntity($ident);

	public function saveEntity(Model\Entity $entity);

	public function deleteEntity(Model\Entity $entity);
	/**
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $target
	 */
	public function moveEntity(Model\Entity $entity, $target);
	/**
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $target
	 */
	public function copyEntity(Model\Entity $entity, $target);

	//                          Model\Entity $entity?
	public function getChildren(Model\Entity $entity, $class_name = false, $deep = false, $throw_exception = true);


	public function reindex(Model\Entity $entity, $deep = false);
}
