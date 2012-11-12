<?php
namespace Vivo\Module\ResourceManager;

use Vivo\IO\FileInputStream;
use Vivo\Module\Exception;
use Vivo\Module\ResourceProviderInterface;
use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\Storage\PathBuilder\PathBuilderInterface;

use Zend\ModuleManager\ModuleManager;

/**
 * ResourceManager
 * Provides access to module resources
 */
class ResourceManager
{
    /**
     * Vivo Module Manager
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
    protected $resourceBase;

    /**
     * Path Builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Constructor
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param string $resourceBase
     * @param PathBuilderInterface $pathBuilder
     */
    public function __construct(ModuleStorageManager $moduleStorageManager,
                                $resourceBase,
                                PathBuilderInterface $pathBuilder)
    {
        $this->moduleStorageManager = $moduleStorageManager;
        $this->resourceBase         = $resourceBase;
        $this->pathBuilder          = $pathBuilder;
    }

    /**
     * Sets the Vivo module manager
     * @param \Zend\ModuleManager\ModuleManager $moduleManager
     */
    public function setModuleManager(ModuleManager $moduleManager)
    {
        $this->moduleManager    = $moduleManager;
    }

    /**
     * Returns the resource data
     * @param string $moduleName
     * @param string $pathToResource
     * @throws \Vivo\Module\Exception\AsyncCallException
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     * @return string
     */
    public function getResource($moduleName, $pathToResource)
    {
        if (!$this->moduleManager) {
            throw new Exception\AsyncCallException(sprintf('%s: Module manager not set', __METHOD__));
        }
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
            $components     = array($this->resourceBase, $pathToResource);
            $pathInModule   = $this->pathBuilder->buildStoragePath($components, false);
            $resource       = $this->moduleStorageManager->getFileData($moduleName, $pathInModule);
        }
        return $resource;
    }

    /**
     * Returns an input stream for the resource
     * @param string $moduleName
     * @param string $pathToResource
     * @throws \Vivo\Module\Exception\AsyncCallException
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     * @return FileInputStream
     */
    public function getResourceStream($moduleName, $pathToResource)
    {
        if (!$this->moduleManager) {
            throw new Exception\AsyncCallException(sprintf('%s: Module manager not set', __METHOD__));
        }
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
            $components     = array($this->resourceBase, $pathToResource);
            $pathInModule   = $this->pathBuilder->buildStoragePath($components, false);
            $stream         = $this->moduleStorageManager->getFileStream($moduleName, $pathInModule);
        }
        return $stream;
    }
}