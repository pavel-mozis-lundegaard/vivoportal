<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\Entity;
use Vivo\Repository\Exception;

/**
 * Watcher
 * Identity Map implementation for the Repository
 */
class Watcher
{
    /**
     * Array of all entities added so far
     * @var Entity[]
     */
    protected $entities		= array();

    /* ********************************** METHODS *********************************** */

    /**
     * Adds an entity to the object watcher
     * @param \Vivo\CMS\Model\Entity $entity
     * @throws Exception\Exception
     */
    public function add(Entity $entity) {
        $globId	= $entity->getUuid();
        if(!$globId) {
            throw new Exception\Exception(sprintf('%s: The entity has no UUID', __METHOD__));
        }
        $this->entities[$globId] = $entity;
    }

    /**
     * Removes entity from the watcher
     * @param string $uuid
     */
    public function remove($uuid) {
        if(array_key_exists($uuid, $this->entities)) {
            unset($this->entities[$uuid]);
        }
    }

    /**
     * Returns entity if it already exists in the watcher, otherwise returns null
     * @param string $uuid
     * @return null|\Vivo\CMS\Model\Entity
     */
    public function get($uuid) {
        $entity	    = null;
        if(array_key_exists($uuid, $this->entities)) {
            $entity = $this->entities[$uuid];
        }
        return $entity;
    }
}