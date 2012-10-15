<?php
namespace Vivo\Storage\StorageCache\KeyNormalizer;

/**
 * KeyNormalizerInterface
 * @author david.lukas
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