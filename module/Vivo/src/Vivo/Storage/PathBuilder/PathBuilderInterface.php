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
}