<?php
namespace Vivo\Module\InstallManager;

use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\CMS\CMS;
use Vivo\CMS\Model\Site;
use Vivo\IO\InputStreamWrapper;

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
     * @param Site|string|null $site If null, checks if the module is installed globally
     * @return bool
     */
    public function isModuleInstalled($module, $site = null)
    {
        if (is_null($site)) {
            return $this->isModuleInstalledInCore($module);
        } else {
            return $this->isModuleInstalledInSite($module, $site);
        }
    }

    /**
     * Returns if a module is installed in a site
     * @param string $module
     * @param Site|string $site
     * @return boolean
     */
    public function isModuleInstalledInSite($module, $site)
    {
        $modules        = $this->getSiteModules($site);
        $isInstalled    = in_array($module, $modules);
        return $isInstalled;
    }

    /**
     * Returns if a module is installed as a core module
     * @param string $module
     * @return boolean
     */
    public function isModuleInstalledInCore($module)
    {
        //TODO - implement isModuleInstalledInCore()
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Installs module into site or globally (core modules)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site Site object or site name or null (for core modules)
     * @throws Exception\ModuleAlreadyInstalledException
     * @throws Exception\NoSiteSpecifiedException
     * @throws Exception\SiteDoesNotExistException
     * @throws Exception\InstallCoreModuleToSiteException
     * @throws Exception\ModuleNotFoundInStorageException
     */
    public function install($module, $site = null)
    {
        //Verify the site exists and get the Site model object
        if (!is_null($site)) {
            if (!$this->cms->siteExists($site)) {
                throw new Exception\SiteDoesNotExistException(
                    sprintf("%s: Site '%' does not exist", __METHOD__, $site));
            }
            $site   = $this->getSite($site);
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
     * @param Site $site
     */
    protected function addModuleToSiteConfig($module, Site $site)
    {
        $modules        = $this->getSiteModules($site);
        $modules[]      = $module;
        //Reset keys
        $modules        = array_values($modules);
        $config['modules']['site_modules']  = $modules;
        $this->cms->setSiteConfig($config, $site);
    }

    /**
     * Returns an array of site modules (module names)
     * @param Site|string $site Site model or site name
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    public function getSiteModules($site)
    {
        $site   = $this->getSite($site);
        $config = $this->cms->getSiteConfig($site);
        if (isset($config['modules']['site_modules'])) {
            $modules    = $config['modules']['site_modules'];
        } else {
            $modules    = array();
        }
        return $modules;
    }

    /**
     * Adds the module to the core config
     * @param string $module
     */
    protected function addModuleToCore($module)
    {
        //TODO - implement addModuleToCore()
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Runs installation script for a module
     * @param string $module
     * @param Site|null $site If null, it is a core module
     */
    protected function runInstallationScript($module, Site $site = null) {
        $installer  = $this->getInstaller($module);
        if ($installer) {

        }
    }

    /**
     * Returns a Site object (model) for the specified site name
     * @param Site|string|null $site
     * @return \Vivo\CMS\Model\Site|null
     * @throws Exception\InvalidArgumentException
     */
    protected function getSite($site = null)
    {
        if (!is_null($site)) {
            if (is_string($site)) {
                $site   = $this->cms->getSite($site);
            }
            if (!$site instanceof Site) {
                throw new Exception\InvalidArgumentException(
                    sprintf('%s: $site must be either a Site model or a site name (string)', __METHOD__));
            }
        }
        return $site;
    }

    /**
     * Instantiates and returns module Installer object
     * If Installer does not exist in the module, returns null
     * @param string $module
     * @return object|null
     */
    protected function getInstaller($module)
    {
        $installerStream    = $this->moduleStorageManager->getFileStream($module, 'Installer.php');
        $path               = 'module-installer.' . $module;
        $installerClass     = $module . '\Installer';
        $installerUrl       = InputStreamWrapper::registerInputStream($installerStream, $path);
        $installer          = null;
        if (file_exists($installerUrl)) {
            include_once $installerUrl;
            if (class_exists($installerClass)) {
                $installer  = new $installerClass();
            }
        }
        return $installer;
    }
}