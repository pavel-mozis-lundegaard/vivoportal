<?php
namespace Vivo\Module\StorageManager;

use Vivo\Storage\StorageInterface;
use Vivo\Module\Exception;
use Vivo\Storage\StorageUtil;

/**
 * StorageManager
 * Module storage manager - performs operations on module storage
 */
class StorageManager
{
    /**
     * Module storage
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Paths in storage, where modules can be found
     * @var array
     */
    protected $modulePaths  = array();

    /**
     * Name of the file containing the JSON data describing the module
     * @var string
     */
    protected $moduleDescriptorName;

    /**
     * Array of info about modules present in storage
     * @var array
     */
    protected $modulesInfo           = array();

    /**
     * Default path in storage, where new modules are installed
     * @var string
     */
    protected $defaultInstallPath;

    /**
     * Storage utilities
     * @var StorageUtil
     */
    protected $storageUtil;

    /**
     * Constructor
     * @param \Vivo\Storage\StorageInterface $storage
     * @param array $modulePaths
     * @param string $moduleDescriptorName
     * @param string|null $defaultInstallPath
     * @param \Vivo\Storage\StorageUtil $storageUtil
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     */
    public function __construct(StorageInterface $storage,
                                array $modulePaths,
                                $moduleDescriptorName,
                                $defaultInstallPath = null,
                                StorageUtil $storageUtil)
    {
        if (is_null($defaultInstallPath)) {
            //Default install path not specified, use the storage root as default
            $defaultInstallPath = $storage->getStoragePathSeparator();
        }
        if (!in_array($defaultInstallPath, $modulePaths)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: The default install path '%s' is not a module path.", __METHOD__, $defaultInstallPath));
        }
        $this->storage              = $storage;
        $this->modulePaths          = $modulePaths;
        $this->moduleDescriptorName = $moduleDescriptorName;
        $this->defaultInstallPath   = $defaultInstallPath;
        $this->storageUtil          = $storageUtil;
    }

    /**
     * Returns an array with information about modules added to the storage
     * @throws \Vivo\Module\Exception\DescriptorException
     * @return array
     */
    public function getModulesInfo()
    {
        if (empty($this->modulesInfo)) {
            $this->modulesInfo   = array();
            foreach ($this->modulePaths as $moduleBasePath) {
                $scan   = $this->storage->scan($moduleBasePath);
                foreach ($scan as $item) {
                    $modulePath     = $this->storage->buildStoragePath(array($moduleBasePath, $item), true);
                    $moduleFilePath = $this->storage->buildStoragePath(array($moduleBasePath, $item, 'Module.php'), true);
                    $moduleJsonPath = $this->storage->buildStoragePath(array($moduleBasePath, $item, self::MODULE_DESCRIPTOR), true);
                    if ($this->storage->isObject($moduleFilePath)) {
                        $moduleJson = $this->getJsonContent($this->storage, $moduleJsonPath);
                        if ((!isset($moduleJson['name'])) || ($item != $moduleJson['name'])) {
                            throw new Exception\DescriptorException(
                                sprintf("%s: Name in the module descriptor does not match module name '%s'.",
                                    __METHOD__, $item));
                        }
                        $moduleDesc = array(
                            'name'          => $item,
                            'storage_path'  => $modulePath,
                            'descriptor'    => $moduleJson,
                        );
                        $this->modulesInfo[$item] = $moduleDesc;
                    }
                }
            }
        }
        return $this->modulesInfo;
    }

    /**
     * Returns info about a module
     * @param string $moduleName
     * @return array
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     */
    public function getModuleInfo($moduleName)
    {
        if (!$this->moduleExists($moduleName)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Module '%s' does not exist", __METHOD__, $moduleName));
        }
        $modulesInfo    = $this->getModulesInfo();
        $moduleInfo     = $modulesInfo[$moduleName];
        return $moduleInfo;
    }

    /**
     * Returns if a module exists in the storage
     * @param string $moduleName
     * @param string|null $version
     * @return boolean
     */
    public function moduleExists($moduleName, $version = null)
    {
        $modules        = $this->getModulesInfo();
        if (!array_key_exists($moduleName, $modules)) {
            return false;
        }
        if (is_null($version)) {
            return true;
        }
        $ourVersion = $modules[$moduleName]['descriptor']['version'];
        $versionOk  = $this->isVersionOk($ourVersion, $version);
        return $versionOk;
    }

    /**
     * Returns true when the specified VModule dependencies are met
     * Detailed info is returned in $info
     * @param array $dependencies
     * @param array $info
     * @return bool
     */
    public function checkVmoduleDependencies(array $dependencies, array &$info)
    {
        $info   = array();
        $result = true;
        foreach ($dependencies as $depName => $depVersion) {
            $info[$depName] = array(
                'name'              => $depName,
                'required_version'  => $depVersion,
                'present_version'   => null,
                'dependency_ok'     => false,
            );
            if ($this->moduleExists($depName)) {
                $moduleInfo = $this->getModuleInfo($depName);
                $info[$depName]['present_version']  = $moduleInfo['descriptor']['version'];
                if ($this->moduleExists($depName, $depVersion)) {
                    $info[$depName]['dependency_ok']    = true;
                } else {
                    $result                             = false;
                    $info[$depName]['dependency_ok']    = false;
                }
            } else {
                $result                             = false;
                $info[$depName]['dependency_ok']    = false;
                $info[$depName]['present_version']  = null;
            }
        }
        return $result;
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

    /**
     * Adds a new module to the storage
     * Does not perform any checks on the source module! To add a new module, use the InstallManager::addModule()
     * @param $sourceStorage
     * @param $pathInSourceStorage
     * @param $moduleName
     * @param null $path
     * @throws \Vivo\Module\Exception\InvalidArgumentException
     */
    public function addModuleToStorage($sourceStorage, $pathInSourceStorage, $moduleName, $path = null)
    {
        if (is_null($path)) {
            $path   = $this->defaultInstallPath;
        } else {
            if (!in_array($path, $this->modulePaths)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: The path '%s' is not a module path.", __METHOD__, $path));
            }
        }
        $fullPath   = $this->storage->buildStoragePath(array($path, $moduleName), true);
        $this->storageUtil->copy($sourceStorage, $pathInSourceStorage, $this->storage, $fullPath);
    }

    /**
     * Checks if a version number satisfies the requirements
     * @param string $ourVersion
     * @param string $requiredVersion May be prepended with an operator '=' or '>='
     * @return bool
     */
    protected function isVersionOk($ourVersion, $requiredVersion)
    {
        $ourVersion         = strtolower($ourVersion);
        $requiredVersion    = strtolower($requiredVersion);
        if (substr($requiredVersion, 0, 2) == '>=') {
            $requiredVersion    = substr($requiredVersion, 2);
            $operator           = '>=';
        } elseif (substr($requiredVersion, 0, 1) == '=') {
            $requiredVersion    = substr($requiredVersion, 1);
            $operator           = '=';
        } else {
            $operator           = '=';
        }
        $result = version_compare($ourVersion, $requiredVersion, $operator);
        return $result;
    }
}