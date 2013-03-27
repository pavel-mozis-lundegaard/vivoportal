<?php
namespace Vivo\Module;

use Zend\Loader\ClassMapAutoloader;
use Zend\Loader\Exception;

/**
 * AutoloaderClassMap
 * Autoloads Vmodule classes from Storage using registered class name -> path pairs
 * @author david.lukas
 */
class AutoloaderClassMap extends ClassMapAutoloader
{
    /**
     * Load a map from a file
     * If the map has been previously loaded, returns the current instance;
     * otherwise, returns whatever was returned by calling include() on the
     * location.
     * @param  string $location
     * @return ClassMapAutoloader|mixed
     * @throws Exception\InvalidArgumentException for nonexistent locations
     */
    protected function loadMapFromFile($location)
    {
        if (!file_exists($location)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException(sprintf(
                'Map file provided does not exist. Map file: "%s"',
                (is_string($location) ? $location : 'unexpected type: ' . gettype($location))
            ));
        }

        if (!$path = static::realPharPath($location)) {
            //realpath does not work with streams
            //$path = realpath($location);
            $path = $location;
        }

        if (in_array($path, $this->mapsLoaded)) {
            // Already loaded this map
            return $this;
        }

        $map = include $path;

        return $map;
    }
}