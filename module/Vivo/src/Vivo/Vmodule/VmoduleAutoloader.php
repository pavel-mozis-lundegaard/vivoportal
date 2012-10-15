<?php
namespace Vivo\Vmodule;

use Zend\Loader\ModuleAutoloader;
use Vivo\Storage\StorageInterface;
use Vivo\Vmodule\VmoduleStreamWrapper;

/**
 * VmoduleAutoloader
 * Autoloads Vmodule Module classes from Storage
 * @author david.lukas
 */
class VmoduleAutoloader extends ModuleAutoloader
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
        $moduleFileUrl = VmoduleStreamWrapper::STREAM_NAME . '://' . $dirPath . '/Module.php';
        require_once $moduleFileUrl;
        if (class_exists($class)) {
            //TODO - review: is it ok to store the URL to the Module file?
            $this->moduleClassMap[$class] = $moduleFileUrl;
            return $class;
        }
        return false;
    }
}
