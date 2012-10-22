<?php
namespace Vivo\Site\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Vivo\Site\Event\SiteEventInterface;
use Vivo\Site\Resolver\ResolverInterface;
use Vivo\Module\ModuleManagerFactory;

/**
 * CreateSiteListener
 * Sets-up a listener for MVC Route event to create and prepare a Site object
 */
class CreateSiteListener implements ListenerAggregateInterface
{
    /**
     * Name used to register the Site object in the ServiceManager
     */
    const SM_KEY_SITE   = 'vivo_site';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

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
     * Constructor
     * @param $routeParamHost
     * @param \Vivo\Site\Resolver\ResolverInterface $resolver Site alias resolver
     * @param \Vivo\Module\ModuleManagerFactory $moduleManagerFactory
     */
    public function __construct($routeParamHost, ResolverInterface $resolver, ModuleManagerFactory $moduleManagerFactory)
    {
        $this->routeParamHost       = $routeParamHost;
        $this->resolver             = $resolver;
        $this->moduleManagerFactory = $moduleManagerFactory;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'));
    }

    /**
     * Detach all our listeners from the event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "route" event, create a new Site and store it in the SM
     * @param  MvcEvent $e
     * @return void
     */
    public function onRoute(MvcEvent $e)
    {
        $sm         = $e->getApplication()->getServiceManager();
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $routeMatch = $e->getRouteMatch();
        $siteEvent  = new \Vivo\Site\Event\SiteEvent();
        $siteEvents = new \Zend\EventManager\EventManager();
        $siteEvent->setParam('route_match', $routeMatch);
        //Attach Site resolve listener
        $resolveListener    = new SiteResolveListener($this->routeParamHost, $this->resolver);
        $resolveListener->attach($siteEvents);
        //Attach Site config listener
        $configListener     = new SiteConfigListener();
        $configListener->attach($siteEvents);
        //Attach Load modules listener
        $loadModulesListener    = new LoadModulesListener($this->moduleManagerFactory);
        $loadModulesListener->attach($siteEvents);
        //Create Site
        $site       = new \Vivo\Site\Site($siteEvents, $siteEvent);
        //Trigger events on Site
        //Init the Site
        $siteEvent->stopPropagation(false);
        $siteEvents->trigger(SiteEventInterface::EVENT_INIT, $siteEvent);
        //Resolve the Site id
        $siteEvent->stopPropagation(false);
        $siteEvents->trigger(SiteEventInterface::EVENT_RESOLVE, $siteEvent);
        //Test if the Site has been resolved
        if ($site->getSiteId()) {
            //The Site has been resolved, so configure it and store it
            //Get Site config
            $siteEvent->stopPropagation(false);
            $siteEvents->trigger(SiteEventInterface::EVENT_CONFIG, $siteEvent);
            //Load site modules
            $siteEvent->stopPropagation(false);
            $siteEvents->trigger(SiteEventInterface::EVENT_LOAD_MODULES, $siteEvent);
            //Store the Site into SM
            $sm->setService(self::SM_KEY_SITE, $site);
        }
    }
}