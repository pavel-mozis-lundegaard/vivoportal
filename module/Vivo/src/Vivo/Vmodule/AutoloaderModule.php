<?php
namespace Vivo\Vmodule;

use Zend\Loader\ModuleAutoloader;
use Vivo\Vmodule\StreamWrapper;
use Vivo\Vmodule\Exception\StreamException;
use Traversable;

/**
 * AutoloaderModule
 * Autoloads Vmodule Module class from Storage
 */
class AutoloaderModule extends ModuleAutoloader
{
    /**
     * Name of the stream (protocol) to access the Vmodule source
     * @var string
     */
    protected $vModuleStreamName;

    /**
     * Constructor
     * @param array|Traversable|null $paths Absolute paths in storage
     * @param string|null $vModuleStreamName
     */
    public function __construct($paths = null, $vModuleStreamName = null)
    {
        parent::__construct($paths);
        $this->setVmoduleStreamName($vModuleStreamName);
    }

    /**
     * Sets the Vmodule stream name
     * @param string $vModuleStreamName
     */
    public function setVmoduleStreamName($vModuleStreamName)
    {
        $this->vModuleStreamName    = $vModuleStreamName;
    }

    /**
     * Loads module from directory
     * @param string $dirPath
     * @param string $class
     * @throws Exception\StreamException
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    protected function loadModuleFromDir($dirPath, $class)
    {
        if (!$this->vModuleStreamName) {
            throw new StreamException(sprintf('%s: Vmodule stream name (protocol) not set.', __METHOD__));
        }
        $moduleFileUrl = $this->vModuleStreamName . '://' . $dirPath . '/Module.php';
        //We must use include not require, otherwise the execution stops when the $moduleFileUrl is not found
        //We are suppressing output in php log, otherwise there are warnings logged
        @include_once $moduleFileUrl;
        if (class_exists($class)) {
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