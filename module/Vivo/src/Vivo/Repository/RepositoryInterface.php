<?php
namespace Vivo\Repository;

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


	/**
	 * @see: self::getEntity($ident)
	 */
// 	public function getEntityPathByUuid($uuid);

	//                          Model\Entity $entity?
	public function getChildren($path = '', $class_name = false, $deep = false, $throw_exception = true);


// 	public function begin();

// 	public function commit();

// 	public function rollback();
}
