<?php
namespace Vivo\Module\InstallManager;

/**
 * InstallManagerInterface
 */
interface InstallManagerInterface
{

    //TODO - check that all methods are defined here

    /**
     * Adds a module into storage
     * @param string $moduleUrl
     * @param bool $force
     * @param string|null $installPath
     * @return mixed
     */
    public function addModule($moduleUrl, $force = false, $installPath = null);

    /*
    public function installModule();

    public function upgradeModule();

    public function uninstallModule();

    public function removeModule();
    */

    /**
     * Returns if a module exists in the storage
     * @param string $moduleName
     * @return boolean
     */
    public function moduleExists($moduleName);

    /**
     * Returns an array with names of modules added to the repo
     * @return array
     */
    public function getModules();
}