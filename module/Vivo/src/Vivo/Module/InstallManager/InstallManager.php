<?php
namespace Vivo\Module\InstallManager;

use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\CMS\CMS;

/**
 * InstallManager
 * Performs module installation tasks
 */
class InstallManager
{
    /**
     * Module storage manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * CMS
     * @var CMS
     */
    protected $cms;

    /**
     * Constructor
     * @param ModuleStorageManager $moduleStorageManager
     * @param \Vivo\CMS\CMS $cms
     */
    public function __construct(ModuleStorageManager $moduleStorageManager, CMS $cms)
    {
        $this->moduleStorageManager = $moduleStorageManager;
        $this->cms                  = $cms;
    }

    /**
     * Checks if a module is installed in a site | globally (a core module)
     * @param string $module Module name (ie module namespace)
     * @param string|null $site If null, checks if the module is installed globally
     * @return bool
     */
    public function isModuleInstalled($module, $site = null)
    {
        return false;
    }

    /**
     * Installs module into site or globally (core modules)
     * @param string $module Module name (ie module namespace)
     * @param string|null $site Site name or null for core modules
     * @throws Exception\ModuleAlreadyInstalledException
     * @throws Exception\NoSiteSpecifiedException
     * @throws Exception\SiteDoesNotExistException
     * @throws Exception\InstallCoreModuleToSiteException
     * @throws Exception\ModuleNotFoundInStorageException
     */
    public function install($module, $site = null)
    {
        //Verify the site exists
        if (!is_null($site)) {
            if (!$this->cms->siteExists($site)) {
                throw new Exception\SiteDoesNotExistException(
                    sprintf("%s: Site '%' does not exist", __METHOD__, $site));
            }
        }
        //Verify the module has not been installed yet
        if ($this->isModuleInstalled($module, $site)) {
            throw new Exception\ModuleAlreadyInstalledException(
                sprintf("%s: Module '%s' is already installed", __METHOD__, $module));
        }
        //Verify the module has been added to the storage
        if (!$this->moduleStorageManager->moduleExists($module)) {
            throw new Exception\ModuleNotFoundInStorageException(
                sprintf("%s: Module '%s' not found in storage", __METHOD__, $module));
        }
        //Verify that site module is not being installed into core and vice versa
        $moduleInfo = $this->moduleStorageManager->getModuleInfo($module);
        $moduleType = $moduleInfo['descriptor']['type'];
        if (!is_null($site) && $moduleType  == 'core') {
            //Installing a core module to a site
            throw new Exception\InstallCoreModuleToSiteException(
                sprintf("%s: Cannot install the core module '%s' to a site", __METHOD__, $module));

        } elseif (is_null($site) && $moduleType == 'site') {
            //Installing site module without site specification
            throw new Exception\NoSiteSpecifiedException(
                sprintf("%s: Site specification missing when installing site module '%s'", __METHOD__, $module));
        }
        //Do the actual installation
        $this->runInstallationScript($module, $site);
        if (is_null($site)) {
            $this->addModuleToCore($module);
        } else {
            $this->addModuleToSiteConfig($module, $site);
        }
    }

    /**
     * Adds the module to the site config
     * @param string $module
     * @param string $siteName
     */
    protected function addModuleToSiteConfig($module, $siteName)
    {
        $site   = $this->cms->getSite($siteName);
        $config = $this->cms->getSiteConfig($site);
        if (isset($config['modules']['site_modules'])) {
            $modules    = $config['modules']['site_modules'];
        } else {
            $modules    = array();
        }
        $modules[]      = $module;
        //Reset keys
        $modules        = array_values($modules);
        $config['modules']['site_modules']  = $modules;
        $this->cms->setSiteConfig($config, $site);

    }

    /**
     * Adds the module to the core config
     * @param string $module
     */
    protected function addModuleToCore($module)
    {
        //TODO - implement
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Runs installation script for a module
     * @param string $module
     * @param string|null $site If null, it is a core module
     */
    protected function runInstallationScript($module, $site = null) {
        //TODO - implement
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }
}