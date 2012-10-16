<?php
namespace Vivo\Vmodule;

use Zend\Loader\ModuleAutoloader;
use Vivo\Vmodule\StreamWrapper;

/**
 * AutoloaderModule
 * Autoloads Vmodule Module class from Storage
 * @author david.lukas
 */
class AutoloaderModule extends ModuleAutoloader
{
    /**
     * loadModuleFromDir
     *
     * @param string $dirPath
     * @param string $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    protected function loadModuleFromDir($dirPath, $class)
    {
        //TODO - configure stream name via DI?
        $moduleFileUrl = StreamWrapper::STREAM_NAME . '://' . $dirPath . '/Module.php';
        require_once $moduleFileUrl;
        if (class_exists($class)) {
            //TODO - review: is it ok to store the URL to the Module file?
            $this->moduleClassMap[$class] = $moduleFileUrl;
            return $class;
        }
        return false;
    }

    /**
     * loadModuleFromPhar
     *
     * @param string $pharPath
     * @param string $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    protected function loadModuleFromPhar($pharPath, $class)
    {
        //TODO - implement loading Vmodule classes from phars?
        throw new \Exception(sprintf('%s: Method not implemented.', __METHOD__));
    }
}