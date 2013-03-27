<?php
namespace Vivo\Storage;

use ArrayAccess;

/**
 * Factory
 * Creates Storage objects based on the submitted configuration
 */
class Factory
{
    /**
     * Creates a new Storage
     * @param array|ArrayAccess $config
     * @throws Exception\InvalidArgumentException
     * @return StorageInterface
     */
    public function create($config)
    {
        if (!(is_array($config) || ($config instanceof ArrayAccess))) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Config must be either an array or must implement ArrayAccess', __METHOD__));
        }
        if (!isset($config['class'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: 'class' key missing in config", __METHOD__));
        }
        $class  = $config['class'];
        if (isset($config['options'])) {
            $options    = $config['options'];
        } else {
            $options    = array();
        }
        $storage    = new $class($options);
        return $storage;
    }
}