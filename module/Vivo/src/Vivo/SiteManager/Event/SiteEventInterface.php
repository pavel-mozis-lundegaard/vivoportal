<?php
namespace Vivo\SiteManager\Event;

use Zend\EventManager\EventInterface;
use Zend\Mvc\Router\RouteMatch;

/**
 * SiteEventInterface
 */
interface SiteEventInterface extends EventInterface
{
    const EVENT_INIT            = 'init';
    const EVENT_RESOLVE         = 'resolve';
    const EVENT_CONFIG          = 'config';
    const EVENT_LOAD_MODULES    = 'load_modules';

    /**
     * Sets the Site ID
     * @param string $siteId
     */
    public function setSiteId($siteId);

    /**
     * Returns the Site ID
     * @return string
     */
    public function getSiteId();

    /**
     * Sets the current host name
     * @param string $host
     */
    public function setHost($host);

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
     * @param RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * Returns the RouteMatch object
     * @return RouteMatch
     */
    public function getRouteMatch();
}
