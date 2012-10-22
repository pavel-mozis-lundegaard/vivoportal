<?php
namespace Vivo\Storage\StorageCache\KeyNormalizer;

/**
 * FileSystemCache
 * Normalizes keys for FileSystem cache
 */
class FileSystemCache implements KeyNormalizerInterface
{
    /**
     * Normalizes key
     * Converts slashes and backslashes to plus (+) signs
     * @param string $key
     * @return string
     */
    public function normalizeKey($key)
    {
        $pattern    = '/[\/\\\]/';
        $normalized = preg_replace($pattern, '+', $key);
        return $normalized;
    }
}