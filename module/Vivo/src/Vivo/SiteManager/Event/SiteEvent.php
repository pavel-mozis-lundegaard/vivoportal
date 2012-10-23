<?php
namespace Vivo\SiteManager\Event;

use Vivo\SiteManager\Exception;

use Zend\EventManager\Event;
use Zend\Mvc\Router\RouteMatch;
use ArrayAccess;

/**
 * SiteEvent
 */
class SiteEvent extends Event implements SiteEventInterface
{
    /**
     * Site ID
     * @var string
     */
    protected $siteId;

    /**
     * Host name currently used to access the site
     * @var string
     */
    protected $host;

    /**
     * Array of module names required by this site
     * @var array
     */
    protected $modules  = array();

    /**
     * Site configuration
     * @var array|ArrayAccess
     */
    protected $siteConfig   = array();

    /**
     * RouteMatch object
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * Sets the Site ID
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Returns the Site ID
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Sets the current host name
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Returns the current host name
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the site configuration
     * @param array|\ArrayAccess $config
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setSiteConfig($config)
    {
        if (!(is_array($config) || $config instanceof ArrayAccess)) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Config must be either an array or must implement ArrayAccess', __METHOD__));
        }
        $this->siteConfig = $config;
    }

    /**
     * Returns the SiteManager configuration
     * @return array|\ArrayAccess
     */
    public function getSiteConfig()
    {
        return $this->siteConfig;
    }

    /**
     * Sets the module names required by this Site
     * @param array $modules
     */
    public function setModules(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * Returns the module names required by this site
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Sets the RouteMatch object
     * @param RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * Returns the RouteMatch object
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }
}
