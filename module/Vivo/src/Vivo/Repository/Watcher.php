<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\PathInterface;
use Vivo\Repository\Exception;

/**
 * Watcher
 * Identity Map implementation for the Repository
 */
class Watcher
{
    /**
     * Array of all entities added so far
     * Keys are entity paths
     * @var PathInterface[]
     */
    protected $entities		    = array();

    /* ********************************** METHODS *********************************** */

    /**
     * Adds an entity to the object watcher
     * @param \Vivo\CMS\Model\PathInterface $entity
     * @throws Exception\Exception
     */
    public function add(PathInterface $entity) {
        $path   = $entity->getPath();
        if (!$path) {
            throw new Exception\Exception(sprintf('%s: The entity (%s) has no path', __METHOD__, get_class($entity)));
        }
        $this->entities[$path]      = $entity;
    }

    /**
     * Removes entity from the watcher
     * @param string $path
     */
    public function remove($path) {
        if (array_key_exists($path, $this->entities)) {
            unset($this->entities[$path]);
        }
    }

    /**
     * Returns an entity from watcher
     * If there is no entity under this path, returns null
     * @param string $path
     * @return null|\Vivo\CMS\Model\Entity
     */
    public function get($path) {
        if (array_key_exists($path, $this->entities)) {
            $entity = $this->entities[$path];
        } else {
            $entity = null;
        }
        return $entity;
    }
}
