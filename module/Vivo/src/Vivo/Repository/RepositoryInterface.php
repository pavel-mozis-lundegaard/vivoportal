<?php
namespace Vivo\Repository;

use Vivo\TransactionalInterface;
use Vivo\CMS\Model;

/**
 * RepositoryInterface
 */
interface RepositoryInterface extends TransactionalInterface {
	/**
     * Returns entity identified by $ident
     * If entity is not found, returns null
	 * @param string $ident Entity identification (path, UUID or symbolic reference)
	 * @return \Vivo\CMS\Model\Entity|null
	 */
	public function getEntity($ident);

    /**
     * Schedules entity for saving into storage
     * @param \Vivo\CMS\Model\Entity $entity
     * @return mixed
     */
    public function saveEntity(Model\Entity $entity);

    /**
     * Schedules entity for deletion form storage
     * @param \Vivo\CMS\Model\Entity $entity
     * @return void
     */
    public function deleteEntity(Model\Entity $entity);

    /**
     * Schedules entity for moving in the storage
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param string $target
	 */
	public function moveEntity(Model\Entity $entity, $target);

    /**
     * Schedules entity for copying in storage
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param string $target
	 */
	public function copyEntity(Model\Entity $entity, $target);

    /**
     * Return subdocuments
     * When $deep == true, returns descendants rather than children
     * @param \Vivo\CMS\Model\Entity $entity
     * @param bool|string $className
     * @param bool $deep
     * @return array
     */
	public function getChildren(Model\Entity $entity, $className = false, $deep = false);

    /**
     * Reindex all entities (contents and children) saved under entity
     * Returns number of reindexed items
     * @param string $path Path to entity
     * @param bool $deep If true reindexes whole subtree
     * @return int
     */
    public function reindex($path, $deep = false);

    /**
     * Schedules resource for deletion from storage
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $name
     * @return void
     */
    public function deleteResource(Model\Entity $entity, $name);

    /**
     * Returns resource from storage
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $name
     * @return string
     */
	public function getResource(Model\Entity $entity, $name);

    /**
     * Returns an input stream for reading from the resource
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $name Resource file name.
     * @return \Vivo\IO\InputStreamInterface
     */
	public function readResource(Model\Entity $entity, $name);

    /**
     * Schedules resource for saving into storage
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $name
     * @param string $data
     * @return void
     */
    public function saveResource(Model\Entity $entity, $name, $data);

    /**
     *
     * Schedules writing to a resource from a stream
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $name
     * @param \Vivo\IO\InputStreamInterface $stream
     * @return void
     */
    public function writeResource(Model\Entity $entity, $name, \Vivo\IO\InputStreamInterface $stream);

    /**
     * Returns parent folder
     * If there is no parent folder (ie this is a root), returns null
     * @param \Vivo\CMS\Model\Folder $folder
     * @return \Vivo\CMS\Model\Folder
     */
	public function getParent(Model\Folder $folder);

    /**
     * Returns true when the folder has children
     * @param Model\Folder $folder
     * @return bool
     */
	public function hasChildren(Model\Folder $folder);
}
