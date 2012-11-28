<?php
namespace Vivo\Controller\CLI;

use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\Module\StorageManager\RemoteModule;
use Vivo\Module\InstallManager\InstallManager;
use Vivo\Module\InstallManager\Exception as InstallException;
use Vivo\Repository\Repository;

use Zend\Console\Request as ConsoleRequest;

/**
 * Vivo CLI controller for command 'module'
 */
class ModuleController extends AbstractCliController
{
    const COMMAND = 'module';

    /**
     * Module Storage Manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * Remote Module
     * @var RemoteModule
     */
    protected $remoteModule;

    /**
     * Module installation manager
     * @var InstallManager
     */
    protected $installManager;

    /**
     * Repository
     * @var Repository
     */
    protected $repository;

    /**
     * Constructor
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param \Vivo\Module\StorageManager\RemoteModule $remoteModule
     * @param \Vivo\Module\InstallManager\InstallManager $installManager
     * @param \Vivo\Repository\Repository $repository
     */
    public function __construct(ModuleStorageManager $moduleStorageManager,
                                RemoteModule $remoteModule,
                                InstallManager $installManager,
                                Repository $repository)
    {
        $this->moduleStorageManager = $moduleStorageManager;
        $this->remoteModule         = $remoteModule;
        $this->installManager       = $installManager;
        $this->repository           = $repository;
    }

    public function defaultAction()
    {
        return $this->listAction();
    }

    public function getConsoleUsage()
    {
        return 'module usage: ...';
    }

    /**
     * Installs a module into a site or globally (a core module)
     * @return string
     */
    public function installAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module install <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt installation
        try {
            $this->installManager->install($module, $site);
            $this->repository->commit();
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (InstallException\ModuleAlreadyInstalledException $e) {
            //Module is already installed
            $output = sprintf("Module '%s' is already installed", $module);
            return $output;
        } catch (InstallException\ModuleNotFoundInStorageException $e) {
            //Module has not been found in storage
            $output = sprintf("Module '%s' not found in storage; add the module first", $module);
            return $output;
        } catch (InstallException\InstallCoreModuleToSiteException $e) {
            //Trying to install a core module to a site
            $output = sprintf("Cannot install the core module '%s' to a site", $module);
            return $output;
        } catch (InstallException\NoSiteSpecifiedException $e) {
            //Trying to install a site module without a site specification
            $output = sprintf("Site specification missing when installing site module '%s'", $module);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during module installation');
            return $output;
        }
        //Everything ok
        if (is_null($site)) {
            $output = sprintf("Core module '%s' has been installed", $module);
        } else {
            $output = sprintf("Module '%s' has been installed into site '%s'", $module, $site);
        }
        return $output;

    }

    public function removeAction()
    {
        //TODO
    }

    /**
     * Lists the modules present in storage
     * @return string
     */
    public function listAction()
    {
        $rowTemplate    = "%-1.1s %-30.30s %-10.10s %-34.34s\n";
        $output         = "\nModules in storage\n\n";
        $output         .= sprintf($rowTemplate, 'C', 'Name', 'Version', 'Description');
        $output         .= str_repeat('-', 78) . "\n";
        $modulesInfo    = $this->moduleStorageManager->getModulesInfo();
        foreach ($modulesInfo as $moduleInfo) {
            $descriptor =  $moduleInfo['descriptor'];
            if (isset($descriptor['description'])) {
                $description    = $descriptor['description'];
            } else {
                $description    = '';
            }
            if ($descriptor['type'] == 'core') {
                $moduleType = 'C';
            } else {
                $moduleType = '';
            }
            $output     .= sprintf($rowTemplate, $moduleType, $moduleInfo['name'],
                                   $descriptor['version'], $description);
        }
        return $output;
    }

    /**
     * Adds a module to storage
     * @return string
     */
    public function addAction()
    {
        //$moduleUrl should be in format: 'file://<full path to the module dir>'
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $moduleUrl  = $request->getParam('module_url');
        if (!$moduleUrl) {
            return 'Usage: module add <module_url> [--force|-f]';
        }
        $force      = $request->getParam('force', false) || $request->getParam('f', false);
        $moduleDescriptor   = $this->remoteModule->getModuleDescriptor($moduleUrl);
        //Check VModule dependencies
        if ((!$force) && (isset($moduleDescriptor['require']))) {
            $dependencies   = $moduleDescriptor['require'];
            $info           = array();
            if (!$this->moduleStorageManager->checkVmoduleDependencies($dependencies, $info)) {
                $output     = sprintf("\nSome dependencies of module '%s' are missing.\n", $moduleDescriptor['name']);
                $output     .= $this->formatDependencyTable($info);
                return $output;
            }
        }
        $moduleInfo = $this->moduleStorageManager->addModule($moduleUrl, $force);
        $output     = sprintf("\nModule '%s' has been added to storage.\n", $moduleInfo['name']);
        $output     .= $this->formatModuleInfo($moduleInfo);
        return $output;
    }

    /**
     * Returns a formatted string containing the dependency table
     * @param array $dependencyTable
     * @return string
     */
    protected function formatDependencyTable(array $dependencyTable)
    {
        $rowTemplate    = "%-30.30s %-14.14s %-14.14s %-5.5s\n";
        $output         = "\nDependency table\n\n";
        $output         .= sprintf($rowTemplate, 'Name', 'Required ver.', 'Current ver.', 'OK');
        $output         .= str_repeat('-', 66) . "\n";
        foreach ($dependencyTable as $row) {
            $output     .= sprintf($rowTemplate,
                            $row['name'],
                            $row['required_version'],
                            $row['present_version'],
                            $row['dependency_ok'] ? 'YES' : 'NO');
        }
        return $output;
    }

    /**
     * Returns a formatted string containing the module info
     * @param array $info
     * @return string
     */
    protected function formatModuleInfo(array $info)
    {
        $rowTemplate    = "%-20.20s %-40.40s\n";
        $output         = "\nModule info\n";
        $output         .= str_repeat('-', 61) . "\n";
        $output         .= sprintf($rowTemplate, 'Name:', $info['name']);
        $output         .= sprintf($rowTemplate, 'Version:', $info['descriptor']['version']);
        $output         .= sprintf($rowTemplate, 'Type:', $info['descriptor']['type']);
        $output         .= sprintf($rowTemplate, 'Storage path:', $info['storage_path']);
        return $output;
    }
}
