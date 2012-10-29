<?php
namespace Vivo\Storage;

/**
 * AbstractStorage
 * Common storage functionality
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Character used as a separator for paths in storage
     * @var string
     */
    protected $storagePathSep   = '/';

    /**
     * Returns character used as a separator in storage paths
     * @return string
     */
    public function getStoragePathSeparator()
    {
        return $this->storagePathSep;
    }

    /**
     * Builds storage path from submitted elements
     * @param array $elements
     * @param bool $absolute If true, builds an absolute path starting with the storage path separator
     * @return string
     */
    public function buildStoragePath(array $elements, $absolute = false)
    {
        $components = array();
        //Get atomic components
        foreach ($elements as $key => $element) {
            $elementComponents  = $this->getStoragePathComponents($element);
            $components         = array_merge($components, $elementComponents);
        }
        $storagePath    = implode($this->getStoragePathSeparator(), $components);
        if ($absolute) {
            $storagePath    = $this->getStoragePathSeparator() . $storagePath;
        }
        return $storagePath;
    }

    /**
     * Returns an array of 'atomic' storage path components
     * @param string $path
     * @return array
     */
    public function getStoragePathComponents($path)
    {
        $components = explode($this->getStoragePathSeparator(), $path);
        foreach ($components as $key => $value) {
            if (empty($value)) {
                unset($components[$key]);
            }
        }
        //Reset array indices
        $components = array_values($components);
        return $components;
    }

    /**
     * Trims storage path separator optionally form beginning and end of path
     * @param string $path
     * @param bool $left
     * @param bool $right
     * @return string
     */
    protected function trimStoragePath($path, $left = true, $right = true)
    {
        if ($left) {
            $path   = ltrim($path, $this->getStoragePathSeparator());
        }
        if ($right) {
            $path   = rtrim($path, $this->getStoragePathSeparator());
        }
        return $path;
    }
}