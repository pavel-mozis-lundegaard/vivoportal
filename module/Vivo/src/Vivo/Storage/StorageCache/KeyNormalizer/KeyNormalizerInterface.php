<?php
namespace Vivo\Storage\StorageCache\KeyNormalizer;

/**
 * KeyNormalizerInterface
 */
interface KeyNormalizerInterface
{
    /**
     * Normalizes key
     * @param string $key
     * @return string
     */
    public function normalizeKey($key);
}