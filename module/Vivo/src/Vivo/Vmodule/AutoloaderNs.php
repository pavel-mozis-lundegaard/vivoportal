<?php
namespace Vivo\Vmodule;

use Zend\Loader\StandardAutoloader;
use Zend\Loader\Exception\InvalidArgumentException;

/**
 * AutoloaderNs
 * Autoloads Vmodule classes from Storage using registered PSR-0 namespace
 * @author david.lukas
 */
class AutoloaderNs extends StandardAutoloader
{
    /**
     * Load a class from a namespace
     * @param string $class
     * @param string $type
     * @return bool|mixed|string
     * @throws \Zend\Loader\Exception\InvalidArgumentException
     */
    protected function loadClass($class, $type)
    {
        if ($type != self::LOAD_NS) {
            throw new InvalidArgumentException(sprintf("%s: Type '%s' not supported.", __METHOD__, $type));
        }
        //Namespace autoloading
        foreach ($this->$type as $leader => $path) {
            if (0 === strpos($class, $leader)) {
                //Trim off leader (namespace i.e. namespace)
                $trimmedClass   = substr($class, strlen($leader));
                //Create filename
                $fileUrl        = $this->transformClassNameToFilename($trimmedClass, $path);
                return include $fileUrl;
            }
        }
        return false;
    }
}