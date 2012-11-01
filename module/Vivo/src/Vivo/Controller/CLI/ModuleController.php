<?php
namespace Vivo\Controller\CLI;

use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\Module\StorageManager\RemoteModule;

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
     * Constructor
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param \Vivo\Module\StorageManager\RemoteModule $remoteModule
     */
    public function __construct(ModuleStorageManager $moduleStorageManager, RemoteModule $remoteModule)
    {
        $this->moduleStorageManager = $moduleStorageManager;
        $this->remoteModule         = $remoteModule;
    }

    public function defaultAction()
    {
        return $this->listAction();
    }

    public function getConsoleUsage()
    {
        return 'module usage: ...';
    }

    public function installAction()
    {
        //TODO
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
        $rowTemplate    = "%-30.30s %-10.10s %-36.36s\n";
        $output         = "\nModules in storage\n\n";
        $output         .= sprintf($rowTemplate, 'Name', 'Version', 'Description');
        $output         .= str_repeat('-', 78) . "\n";
        $modulesInfo    = $this->moduleStorageManager->getModulesInfo();
        foreach ($modulesInfo as $moduleInfo) {
            $descriptor =  $moduleInfo['descriptor'];
            if (isset($descriptor['description'])) {
                $description    = $descriptor['description'];
            } else {
                $description    = '';
            }
            $output     .= sprintf($rowTemplate, $moduleInfo['name'], $descriptor['version'], $description);
        }
        return $output;
    }

    /**
     * Adds a module to storage
     * @return string
     */
    public function addAction()
    {
        //$moduleUrl should be in format: 'file://c:\Work\DummyModules\Vm10'
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
