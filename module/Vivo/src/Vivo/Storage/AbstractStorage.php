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
        //Trim all elements
        foreach ($elements as $key => $element) {
            $trimmed        = $this->trimStoragePath($element, true, true);
            if ($trimmed != '') {
                $elements[$key] = $trimmed;
            } else {
                unset($elements[$key]);
            }
        }
        $storagePath    = implode($this->getStoragePathSeparator(), $elements);
        if ($absolute) {
            $storagePath    = $this->getStoragePathSeparator() . $storagePath;
        }
        return $storagePath;
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