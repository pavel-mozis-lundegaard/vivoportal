<?php
namespace Vivo\SiteManager;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\SiteManager\Resolver\ResolverInterface;
use Vivo\SiteManager\Listener\SiteResolveListener;
use Vivo\SiteManager\Listener\SiteConfigListener;
use Vivo\SiteManager\Listener\LoadModulesListener;
use Vivo\SiteManager\Listener\CollectModulesListener;
use Vivo\Module\ModuleManagerFactory;

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
     * @var ResolverInterface
     */
    protected $resolver;

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
     * Constructor
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param Event\SiteEventInterface $siteEvent
     * @param string $routeParamHost Name of the route parameter containing the host name
     * @param Resolver\ResolverInterface $resolver
     * @param \Vivo\Module\ModuleManagerFactory $moduleManagerFactory
     * @param array $globalModules
     * @param \Zend\Mvc\Router\RouteMatch|null $routeMatch
     */
    public function __construct(EventManagerInterface $events,
                                SiteEventInterface $siteEvent,
                                $routeParamHost,
                                ResolverInterface $resolver,
                                ModuleManagerFactory $moduleManagerFactory,
                                array $globalModules,
                                RouteMatch $routeMatch = null)
    {
        $this->setEventManager($events);
        $this->siteEvent            = $siteEvent;
        $this->routeParamHost       = $routeParamHost;
        $this->resolver             = $resolver;
        $this->moduleManagerFactory = $moduleManagerFactory;
        $this->globalModules        = $globalModules;
        $this->setRouteMatch($routeMatch);
    }

    /**
     * Bootstraps the SiteManager
     */
    public function bootstrap()
    {
        $this->siteEvent->setTarget($this);
        $this->siteEvent->setRouteMatch($this->routeMatch);

        //Attach Site resolve listener
        $resolveListener    = new SiteResolveListener($this->routeParamHost, $this->resolver);
        $resolveListener->attach($this->events);
        //Attach Site config listener
        $configListener     = new SiteConfigListener();
        $configListener->attach($this->events);
        //Attach Collect modules listener
        $collectModulesListener = new CollectModulesListener($this->globalModules);
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
        //Init the Site
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_INIT, $this->siteEvent);
        //Resolve the Site id
        $this->siteEvent->stopPropagation(false);
        $this->events->trigger(SiteEventInterface::EVENT_RESOLVE, $this->siteEvent);
        //Test if the Site has been resolved
        if ($this->siteEvent->getSiteId()) {
            //The Site has been resolved, so configure it and store it
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