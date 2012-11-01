<?php
namespace Vivo\Module\ResourceManager;

use Vivo\IO\FileInputStream;
use Vivo\Module\Exception;
use Vivo\Module\ResourceProviderInterface;
use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;

use Zend\ModuleManager\ModuleManager;

/**
 * ResourceManager
 * Provides access to module resources
 */
class ResourceManager
{
    /**
     * Module Manager
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * Module storage manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * Storage path where resources are placed in a module (relative to module root)
     * @var string
     */
    protected $resourcePath;

    /**
     * Constructor
     * @param \Zend\ModuleManager\ModuleManager $moduleManager
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param string $resourcePath
     */
    public function __construct(ModuleManager $moduleManager, ModuleStorageManager $moduleStorageManager, $resourcePath)
    {
        $this->moduleManager        = $moduleManager;
        $this->moduleStorageManager = $moduleStorageManager;
        $this->resourcePath         = $resourcePath;
    }

    /**
     * Returns the resource data
     * @param string $moduleName
     * @param string $pathToResource
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     * @return string
     */
    public function getResource($moduleName, $pathToResource)
    {
        $module = $this->moduleManager->getModule($moduleName);
        if (!$module) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Module '%s' not loaded", __METHOD__, $moduleName));
        }
        if ($module instanceof ResourceProviderInterface) {
            //Delegate resource retrieval to the module
            /* @var $module ResourceProviderInterface */
            $resource   = $module->getResource($pathToResource);
        } else {
            //Retrieve the resource manually
            $compo
            $this->moduleStorageManager->getFile($moduleName, );

            $elements   = array($this->resourcePath, $pathToResource);
            $fullPath   = $this->storage->buildStoragePath($elements, true);
            if (!$this->storage->isObject($fullPath)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Resource '%s' not found in module '%s'", __METHOD__, $pathToResource, $moduleName));
            }
            $resource   = $this->storage->get($fullPath);
        }
        return $resource;
    }

    /**
     * Returns an input stream for the resource
     * @param string $moduleName
     * @param string $pathToResource
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     * @return FileInputStream
     */
    public function getResourceStream($moduleName, $pathToResource)
    {
        $module = $this->moduleManager->getModule($moduleName);
        if (!$module) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Module '%s' not loaded", __METHOD__, $moduleName));
        }
        if ($module instanceof ResourceProviderInterface) {
            //Delegate resource retrieval to the module
            /* @var $module ResourceProviderInterface */
            $stream   = $module->getResourceStream($pathToResource);
        } else {
            //Retrieve the resource manually
            $elements   = array($this->resourcePath, $pathToResource);
            $fullPath   = $this->storage->buildStoragePath($elements, true);
            if (!$this->storage->isObject($fullPath)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Resource '%s' not found in module '%s'", __METHOD__, $pathToResource, $moduleName));
            }
            $stream   = $this->storage->read($fullPath);
        }
        return $stream;
    }
}