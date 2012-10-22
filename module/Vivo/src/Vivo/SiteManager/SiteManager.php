<?php
namespace Vivo\SiteManager;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\ModuleManager\ModuleManager;
use ArrayAccess;

/**
 * SiteManager
 */
class SiteManager implements SiteManagerInterface,
                             EventsCapableInterface
{
    /**
     * Event manager
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * SiteEvent token
     * @var SiteEventInterface
     */
    protected $siteEvent;

    /**
     * SiteManager ID
     * @var string
     */
    protected $siteId;

    /**
     * SiteManager alias currently used to access the site
     * @var string
     */
    protected $siteAlias;

    /**
     * Array of module names required by this site
     * @var array
     */
    protected $modules  = array();

    /**
     * SiteManager configuration
     * @var array|ArrayAccess
     */
    protected $config   = array();

    /**
     * Module manager
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * Constructor
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param Event\SiteEventInterface $siteEvent
     */
    public function __construct(EventManagerInterface $events, SiteEventInterface $siteEvent)
    {
        $this->events       = $events;
        $this->siteEvent    = $siteEvent;
        $this->siteEvent->setTarget($this);

    }

    /**
     * Retrieve the event manager
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Sets the SiteManager ID
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Returns the SiteManager ID
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Sets the current SiteManager alias
     * @param string $siteAlias
     */
    public function setSiteAlias($siteAlias)
    {
        $this->siteAlias = $siteAlias;
    }

    /**
     * Returns the current SiteManager alias
     * @return string
     */
    public function getSiteAlias()
    {
        return $this->siteAlias;
    }

    /**
     * Sets the site configuration
     * @param array|\ArrayAccess $config
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setConfig($config)
    {
        if (!(is_array($config) || $config instanceof ArrayAccess)) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Config must be either an array or must implement ArrayAccess', __METHOD__));
        }
        $this->config = $config;
    }

    /**
     * Returns the SiteManager configuration
     * @return array|\ArrayAccess
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the module names required by this SiteManager
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
     * Sets the module manager
     * @param ModuleManager $moduleManager
     */
    public function setModuleManager(ModuleManager $moduleManager)
    {
        $this->moduleManager    = $moduleManager;
    }

    /**
     * Returns the module manager
     * @return ModuleManager
     */
    public function getModuleManager()
    {
        return $this->moduleManager;
    }
}