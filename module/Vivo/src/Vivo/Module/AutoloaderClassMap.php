<?php
namespace Vivo\Module;

use Zend\Loader\ClassMapAutoloader;
use Vivo\Module\Exception\StreamException;

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
     * @throws Exception\StreamException
     * @return ClassMapAutoloader|mixed
     */
    protected function loadMapFromFile($location)
    {
        //TODO - Phar support?
        $path   = $location;
        if (in_array($path, $this->mapsLoaded)) {
            // Already loaded this map
            return $this;
        }
        $map = include $path;
        if ($map === false) {
            throw new StreamException(sprintf("%s: Loading class map from path '%s' failed.", __METHOD__, $path));
        }
        return $map;
    }
}