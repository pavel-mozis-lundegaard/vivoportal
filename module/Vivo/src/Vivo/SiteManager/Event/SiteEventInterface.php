<?php
namespace Vivo\SiteManager\Event;

use Vivo\CMS\Model\Site as SiteModel;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Router\RouteMatch;

/**
 * SiteEventInterface
 */
interface SiteEventInterface extends EventInterface
{
    const EVENT_SITE_MODEL_LOAD     = 'site_model_load';
    const EVENT_CONFIG              = 'config';
    const EVENT_COLLECT_MODULES     = 'collect_modules';
    const EVENT_LOAD_MODULES        = 'load_modules';
    const EVENT_LOAD_MODULES_POST   = 'load_modules.post';

    /**
     * Sets the Site ID
     * @param string|null $siteId
     */
    public function setSiteId($siteId = null);

    /**
     * Returns the Site ID
     * @return string
     */
    public function getSiteId();

    /**
     * Sets the site model
     * @param SiteModel|null $site
     * @return void
     */
    public function setSite(SiteModel $site = null);

    /**
     * Returns the site model
     * @return SiteModel
     */
    public function getSite();

    /**
     * Sets the current host name
     * @param string|null $host
     */
    public function setHost($host = null);

    /**
     * Returns the current host name
     * @return string
     */
    public function getHost();

    /**
     * Sets the site configuration
     * @param array|\ArrayAccess $config
     * @return void
     */
    public function setSiteConfig($config);

    /**
     * Returns the Site configuration
     * @return array|\ArrayAccess
     */
    public function getSiteConfig();

    /**
     * Sets the backend configuration
     * @param array|\ArrayAccess $config
     * @return void
     */
    public function setBackendConfig($config);

    /**
     * Returns backend config
     * @return array|\ArrayAccess|null
     */
    public function getBackendConfig();

    /**
     * Sets the module names required by this Site
     * @param array $modules
     */
    public function setModules(array $modules);

    /**
     * Returns the module names required by this site
     * @return array
     */
    public function getModules();

    /**
     * Sets the RouteMatch object
     * @param RouteMatch|null $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch = null);

    /**
     * Returns the RouteMatch object
     * @return RouteMatch
     */
    public function getRouteMatch();

    /**
     * Sets the module manager
     * @param ModuleManager|null $moduleManager
     * @return void
     */
    public function setModuleManager(ModuleManager $moduleManager = null);

    /**
     * Returns the module manager
     * @return ModuleManager
     */
    public function getModuleManager();
}
