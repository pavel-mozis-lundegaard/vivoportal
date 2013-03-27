<?php
namespace Vivo\CMS\Api;

use Vivo\Module\InstallManager\InstallManagerInterface;
use Vivo\CMS\Model\Site;

/**
 * Module
 * Module API
 */
class Module
{
    /**
     * Install manager
     * @var InstallManagerInterface
     */
    protected $installManager;

    /**
     * Constructor
     * @param \Vivo\Module\InstallManager\InstallManagerInterface $installManager
     */
    public function __construct(InstallManagerInterface $installManager)
    {
        $this->installManager   = $installManager;
    }

    /**
     * Checks if a module is installed in a site | globally (a core module)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null, checks if the module is installed globally
     * @return bool
     */
    public function isInstalled($module, $site = null)
    {
        return $this->installManager->isInstalled($module, $site);
    }

    /**
     * Checks if a module is enabled in a site | globally (a core module)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null, checks if the module is enabled globally
     * @return bool
     */
    public function isEnabled($module, $site = null)
    {
        return $this->installManager->isEnabled($module, $site);
    }

    /**
     * Returns a list of modules installed for a site or globally (core modules)
     * @param Site|string|null $site Null = core modules
     * @return array
     */
    public function getInstalledModules($site = null)
    {
        return $this->installManager->getInstalledModules($site);
    }

    /**
     * Returns a list of modules enabled for a site or globally (core modules)
     * @param Site|string|null $site Null = core modules
     * @return array
     */
    public function getEnabledModules($site = null)
    {
        return $this->installManager->getEnabledModules($site);
    }

    /**
     * Installs module into a site or globally (core modules)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null installs into global scope (core modules)
     */
    public function install($module, $site = null)
    {
        $this->installManager->install($module, $site);
    }

    /**
     * Uninstalls a module from a site or from global scope (core modules)
     * @param string $module
     * @param Site|string|null $site If null uninstalls from the global scope (core modules)
     */
    public function uninstall($module, $site = null)
    {
        $this->installManager->uninstall($module, $site);
    }

    /**
     * Enables module for a site or globally (core modules)
     * @param string $module
     * @param Site|string|null $site
     */
    public function enable($module, $site = null)
    {
        $this->installManager->enable($module, $site);
    }

    /**
     * Disables module for a site or globally (core modules)
     * @param string $module
     * @param Site|string|null $site
     */
    public function disable($module, $site = null)
    {
        $this->installManager->disable($module, $site);
    }
}