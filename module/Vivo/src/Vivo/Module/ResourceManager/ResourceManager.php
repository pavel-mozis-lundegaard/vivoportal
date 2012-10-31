<?php
namespace Vivo\Module\ResourceManager;

use Vivo\Storage\StorageInterface;
use Vivo\IO\FileInputStream;
use Vivo\Module\Exception;
use Vivo\Module\ResourceProviderInterface;

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
     * Module Storage
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Path to module resources
     * @var string
     */
    protected $resourcePath;

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
            /* @var $module ResourceProviderInterface */
            $resource   = $module->getResource($pathToResource);
        } else {
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
            /* @var $module ResourceProviderInterface */
            $stream   = $module->getResourceStream($pathToResource);
        } else {
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