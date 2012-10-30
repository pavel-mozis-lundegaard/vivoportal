<?php
namespace Vivo\Module\InstallManager;

use Vivo\Module\Exception;
use Vivo\Storage\StorageUtil;
use Vivo\Storage\Factory as StorageFactory;
use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\Storage\StorageInterface;

/**
 * InstallManager
 */
class InstallManager
{
    /**
     * Name of the file containing the JSON data describing the module
     * @var string
     */
    protected $moduleDescriptorName;

    /**
     * Module Storage Manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * Storage Factory
     * @var StorageFactory
     */
    protected $storageFactory;

    /**
     * Cached module storage instances
     * @var array
     */
    protected $storageInstances     = array();

    /**
     * Constructor
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param \Vivo\Storage\Factory $storageFactory
     * @param string $moduleDescriptorName
     */
    public function __construct(ModuleStorageManager $moduleStorageManager,
                                StorageFactory $storageFactory,
                                $moduleDescriptorName)
    {
        $this->moduleStorageManager         = $moduleStorageManager;
        $this->storageFactory               = $storageFactory;
        $this->moduleDescriptorName         = $moduleDescriptorName;
    }

    /**
     * Adds a module into storage
     * Returns module info on success
     * @param string $moduleUrl
     * @param bool $force
     * @param string|null $path Path where to add the module to
     * @throws \Vivo\Module\Exception\DependencyException
     * @throws \Vivo\Module\Exception\DescriptorException
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     * @return array
     */
    public function addModule($moduleUrl, $force = false, $path = null)
    {
        $sourceStorage          = $this->getModuleStorage($moduleUrl);
        $pathInSourceStorage    = $this->getModulePathInStorage($moduleUrl);
        $moduleDescriptor       = $this->getModuleDescriptor($moduleUrl);
        if (!$moduleDescriptor) {
            throw new Exception\DescriptorException(
                sprintf("%s: Cannot read module descriptor from '%s' for module URL '%s'",
                    __METHOD__, $this->moduleDescriptorName, $moduleUrl));
        }
        if (!isset($moduleDescriptor['name'])) {
            throw new Exception\DescriptorException(
                sprintf("%s: 'name' field missing in module descriptor for module URL '%s'", __METHOD__, $moduleUrl));
        }
        $moduleName = $moduleDescriptor['name'];
        //Check that the module name has not been added yet
        if ($this->moduleStorageManager->moduleExists($moduleName)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Module '%s' (%s) already exists in module storage", __METHOD__, $moduleName, $moduleUrl));
        }
        //Read module dependencies on other vmodules and check they have been added (otherwise throw an exception)
        if (isset($moduleDescriptor['require'])) {
            $dependencies   = $moduleDescriptor['require'];
            $info           = array();
            if (!$this->moduleStorageManager->checkVmoduleDependencies($dependencies, $info)) {
                throw new Exception\DependencyException(
                    sprintf("%s: Dependencies of module '%s' are not satisfied.", __METHOD__, $moduleName));
            }
        }
        //TODO - Read module dependencies on libraries, throw an exception, if unsatisfied
        //Copy the module source to the module storage
        $this->moduleStorageManager->addModuleToStorage($sourceStorage, $pathInSourceStorage, $moduleName, $path);
        $moduleInfo = $this->moduleStorageManager->getModuleInfo($moduleName);
        return $moduleInfo;
    }

    /**
     * Returns storage which will be used to access the source module
     * @param string $moduleUrl
     * @return StorageInterface
     */
    protected function getModuleStorage($moduleUrl)
    {
        if (!array_key_exists($moduleUrl, $this->storageInstances)) {
            //TODO - configure for specific storage implementation based on the $moduleUrl
            $streamSpec     = 'file://';
            $root           = substr($moduleUrl, strlen($streamSpec));
            $storageConfig  = array(
                'class'     => 'Vivo\Storage\LocalFileSystemStorage',
                'options'   => array(
                    'root'      => $root,
                ),
            );
            $this->storageInstances[$moduleUrl] = $this->storageFactory->create($storageConfig);

        }
        return $this->storageInstances[$moduleUrl];
    }

    /**
     * Returns module descriptor from a module identified by its URL
     * @param string $moduleUrl
     * @return array|null
     */
    public function getModuleDescriptor($moduleUrl)
    {
        $storage                = $this->getModuleStorage($moduleUrl);
        $pathInStorage          = $this->getModulePathInStorage($moduleUrl);
        $moduleDescriptorPath   = $storage->buildStoragePath(array($pathInStorage, $this->moduleDescriptorName), true);
        $moduleDescriptor       = $this->getJsonContent($storage, $moduleDescriptorPath);
        return $moduleDescriptor;
    }

    /**
     * Returns the path to module in the source storage
     * @param string $moduleUrl
     * @return string
     */
    protected function getModulePathInStorage($moduleUrl)
    {
        $sourceStorage  = $this->getModuleStorage($moduleUrl);
        //TODO - implement based on the $moduleUrl
        //For file system storage the module is always at the root of the storage
        $modulePath = $sourceStorage->getStoragePathSeparator();
        return $modulePath;
    }

    /**
     * Returns an array with values from a json file
     * If the file cannot be found in storage, returns null
     * @param \Vivo\Storage\StorageInterface $storage
     * @param string $path
     * @return array|null
     */
    protected function getJsonContent(StorageInterface $storage, $path)
    {
        if ($storage->isObject($path)) {
            $data   = $storage->get($path);
            $jsonContent    = json_decode($data, true);
        } else {
            $jsonContent    = null;
        }
        return $jsonContent;
    }

}
