<?php
namespace Vivo\Storage\StorageCache;

use Vivo\Storage\StorageInterface;
use Zend\Cache\Storage\StorageInterface as ZendCache;

use Vivo\Storage\Exception\StorageException;

/**
 * StorageCache
 */
class StorageCache implements StorageCacheInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ZendCache
     */
    protected $cache;

    /**
     * Constructor
     * @param \Zend\Cache\Storage\StorageInterface $cache
     * @param \Vivo\Storage\StorageInterface $storage
     */
    public function __construct(ZendCache $cache, StorageInterface $storage)
    {
        $this->cache    = $cache;
        $this->storage  = $storage;
    }

    /**
     * Checks whether item exists
     * @param string $path to item
     * @return boolean TRUE if item exists otherwise FALSE
     */
    public function contains($path)
    {
        return $this->cache->hasItem($path);
    }

    /**
     * Checks whether item on the given path is an object.
     * @param string $path Path to the item
     * @return bool
     */
    public function isObject($path)
    {
        //TODO - review isObject() implementation - is it ok to check if the cache hasItem?
        if ($this->cache->hasItem($path)) {
            return true;
        }
        return $this->storage->isObject($path);
    }

    /**
     * Returns item modification time in milliseconds.
     * @param string $path to item
     * @return mixed item modification time in milliseconds or FALSE if item doesn't exist
     */
    public function mtime($path)
    {
        //TODO - try to get mtime first from cache?
        return $this->storage->mtime($path);
    }

    /**
     * Returns item from cache, if not there, from the underlying storage
     * @param string $path to item
     * @return mixed|null
     */
    public function get($path)
    {
        $success    = null;
        $item       = $this->cache->getItem($path, $success);
        if (!$success) {
            $item   = $this->storage->get($path);
            //TODO - process the result?
            $result = $this->cache->setItem($path, $item);
        }
        return $item;
    }

    /**
     * Saves item to cache as well as to the underlying storage (WRITE-THROUGH cache)
     * If an item under specified path already exists, it will be overwritten.
     * @param string $path to item
     * @param mixed $variable data
     */
    public function set($path, $variable)
    {
        //TODO - process the result?
        $result = $this->cache->setItem($path, $variable);
        $this->storage->set($path, $variable);
    }

    /**
     * Touches an item
     * Removes the item from the cache and touches the item in the underlying storage
     * @param string $path to item
     */
    public function touch($path)
    {
        //TODO - review item touching - is it ok, to remove it from the cache and touch it in the storage?
        $this->cache->removeItem($path);
        $this->storage->touch($path);
    }

    /**
     * Renames/moves item in the cache as well as in the underlying storage
     * @param string $path to item
     * @param string $target path
     */
    public function move($path, $target)
    {
        $success    = null;
        $cachedItem = $this->cache->getItem($path, $success);
        if ($success) {
            //Item found in the cache
            $this->cache->removeItem($path);
            $this->cache->setItem($target, $cachedItem);
        }
        $this->storage->move($path, $target);
    }

    /**
     * Copies item to another location in the cache as well as in the underlying storage
     * @param string $path to item
     * @param string $target path to copy
     */
    public function copy($path, $target)
    {
        $success    = null;
        $cachedItem = $this->cache->getItem($path, $success);
        if ($success) {
            //Item found in the cache
            $this->cache->setItem($target, $cachedItem);
        }
        $this->storage->copy($path, $target);
    }

    /**
     * Removes item from specified path in the cache as well as in the storage
     * If the item doesn't exist, nothing happens
     * @param string $path to item
     * @return boolean TRUE on success, FALSE if item doesn't exist
     */
    public function remove($path)
    {
        $this->cache->removeItem($path);
        $this->storage->remove($path);
    }

    /**
     * Scans the storage and returns list of child items
     * @param string $path to item
     */
    public function scan($path)
    {
        return $this->storage->scan($path);
    }
}