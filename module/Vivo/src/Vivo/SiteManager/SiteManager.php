<?php
namespace Vivo\SiteManager;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\SiteManager\Listener\SiteModelLoadListener;
use Vivo\SiteManager\Listener\SiteConfigListener;
use Vivo\SiteManager\Listener\LoadModulesListener;
use Vivo\SiteManager\Listener\CollectModulesListener;
use Vivo\Module\ModuleManagerFactory;
use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\CMS\CMS;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Router\RouteMatch;

/**
 * SiteManager
 */
class SiteManager implements SiteManagerInterface,
                             EventManagerAwareInterface
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
     * Module manager
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * RouteMatch object
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * Name of the route param containing the host name
     * @var string
     */
    protected $routeParamHost;

    /**
     * Module manager factory
     * @var ModuleManagerFactory
     */
    protected $moduleManagerFactory;

    /**
     * List of names of global modules
     * @var array
     */
    protected $globalModules    = array();

    /**
     * Module Storage Manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * CMS object
     * @var CMS
     */
    protected $cms;

    public function __construct(EventManagerInterface $events,
                                SiteEventInterface $siteEvent,
                                $routeParamHost,
                                ModuleManagerFactory $moduleManagerFactory,
                                array $globalModules,
                                ModuleStorageManager $moduleStorageManager,
                                CMS $cms,
                                RouteMatch $routeMatch = null)
    {
        $this->setEventManager($events);
        $this->siteEvent            = $siteEvent;
        $this->routeParamHost       = $routeParamHost;
        $this->moduleManagerFactory = $moduleManagerFactory;
        $this->globalModules        = $globalModules;
        $this->moduleStorageManager = $moduleStorageManager;
        $this->cms                  = $cms;
        $this->setRouteMatch($routeMatch);
    }

    /**
     * Bootstraps the SiteManager
     */
    public function bootstrap()
    {
        $this->siteEvent->setTarget($this);
        $this->siteEvent->setRouteMatch($this->routeMatch);

        //Attach Site model load listener
        $configListener         = new SiteModelLoadListener($this->routeParamHost, $this->cms);
        $configListener->attach($this->events);
        //Attach Site config listener
        $configListener         = new SiteConfigListener($this->cms);
        $configListener->attach($this->events);
        //Attach Collect modules listener
        $collectModulesListener = new CollectModulesListener($this->globalModules, $this->moduleStorageManager);
        $collectModulesListener->attach($this->events);
        //Attach Load modules listener
        $loadModulesListener    = new LoadModulesListener($this->moduleManagerFactory);
        $loadModulesListener->attach($this->events);
    }

    /**
     * Prepares the site
     */
    public function prepareSite()
    {
        //Trigger events
        //Load the Site model
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_SITE_MODEL_LOAD, $this->siteEvent);
        //Get Site config
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_CONFIG, $this->siteEvent);
        //Get module names loaded for the site
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_COLLECT_MODULES, $this->siteEvent);
        //Load site modules
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_LOAD_MODULES, $this->siteEvent);
    }

    /**
     * Inject an EventManager instance
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events   = $eventManager;
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
     * Sets the RouteMatch object
     * @param \Zend\Mvc\Router\RouteMatch|null $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch = null)
    {
        $this->routeMatch = $routeMatch;
    }
}