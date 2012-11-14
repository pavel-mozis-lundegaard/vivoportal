<?php
namespace Vivo\Storage\PathBuilder;

/**
 * PathBuilderInterface
 */
interface PathBuilderInterface
{
    /**
     * Returns character used as a separator in storage paths
     * @return string
     */
    public function getStoragePathSeparator();

    /**
     * Builds storage path from submitted elements
     * @param array $elements
     * @param bool $absolute If true, builds an absolute path starting with the storage path separator
     * @return string
     */
    public function buildStoragePath(array $elements, $absolute = true);

    /**
     * Returns an array of 'atomic' storage path components
     * @param string $path
     * @return array
     */
    public function getStoragePathComponents($path);

    /**
     * Returns true when the $path denotes an absolute path
     * @param string $path
     * @return boolean
     */
    public function isAbsolute($path);

    /**
     * Returns directory name for the given path
     * If there is no parent directory for the given $path, returns null
     * @param string $path
     * @return string|null
     */
    public function dirname($path);
}