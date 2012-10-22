<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Resolver\ResolverInterface;
use Vivo\Module\ModuleManagerFactory;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * CreateSiteListener
 * Sets-up a listener for MVC Route event to create and prepare a SiteManager object
 */
class CreateSiteListener implements ListenerAggregateInterface
{
    /**
     * Name used to register the SiteManager object in the ServiceManager
     */
    const SM_KEY_SITE   = 'vivo_site_manager';

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
     * @param \Vivo\SiteManager\Resolver\ResolverInterface $resolver SiteManager alias resolver
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
     * Listen to the "route" event, create a new SiteManager and store it in the SM
     * @param  MvcEvent $e
     * @return void
     */
    public function onRoute(MvcEvent $e)
    {
        $sm         = $e->getApplication()->getServiceManager();
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $routeMatch = $e->getRouteMatch();
        $siteEvent  = new \Vivo\SiteManager\Event\SiteEvent();
        $siteEvents = new \Zend\EventManager\EventManager();
        $siteEvent->setParam('route_match', $routeMatch);
        //Attach SiteManager resolve listener
        $resolveListener    = new SiteResolveListener($this->routeParamHost, $this->resolver);
        $resolveListener->attach($siteEvents);
        //Attach SiteManager config listener
        $configListener     = new SiteConfigListener();
        $configListener->attach($siteEvents);
        //Attach Load modules listener
        $loadModulesListener    = new LoadModulesListener($this->moduleManagerFactory);
        $loadModulesListener->attach($siteEvents);
        //Create SiteManager
        $site       = new \Vivo\SiteManager\SiteManager($siteEvents, $siteEvent);
        //Trigger events on SiteManager
        //Init the SiteManager
        $siteEvent->stopPropagation(false);
        $siteEvents->trigger(SiteEventInterface::EVENT_INIT, $siteEvent);
        //Resolve the SiteManager id
        $siteEvent->stopPropagation(false);
        $siteEvents->trigger(SiteEventInterface::EVENT_RESOLVE, $siteEvent);
        //Test if the SiteManager has been resolved
        if ($site->getSiteId()) {
            //The SiteManager has been resolved, so configure it and store it
            //Get SiteManager config
            $siteEvent->stopPropagation(false);
            $siteEvents->trigger(SiteEventInterface::EVENT_CONFIG, $siteEvent);
            //Load site modules
            $siteEvent->stopPropagation(false);
            $siteEvents->trigger(SiteEventInterface::EVENT_LOAD_MODULES, $siteEvent);
            //Store the SiteManager into SM
            $sm->setService(self::SM_KEY_SITE, $site);
        }
    }
}