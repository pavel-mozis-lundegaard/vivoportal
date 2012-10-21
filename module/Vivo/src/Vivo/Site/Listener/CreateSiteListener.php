<?php
namespace Vivo\Site\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Vivo\Site\Event\SiteEventInterface;
use Vivo\Site\Resolver\ResolverInterface;

/**
 * CreateSiteListener
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
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * Constructor
     * @param \Vivo\Site\Resolver\ResolverInterface $resolver Site alias resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
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
        $routeMatch = $e->getRouteMatch();
        $siteEvent  = new \Vivo\Site\Event\SiteEvent();
        $siteEvents = new \Zend\EventManager\EventManager();
        $siteEvent->setParam('route_match', $routeMatch);
        //Attach Site resolve listener
        $resolveListener    = new SiteResolveListener($this->resolver);
        $resolveListener->attach($siteEvents);
        //Attach Site config listener
        $configListener     = new SiteConfigListener();
        $configListener->attach($siteEvents);
        //Attach Load modules listener
        $loadModulesListener    = new LoadModulesListener();
        $loadModulesListener->attach($siteEvents);
        //Create Site
        $site       = new \Vivo\Site\Site($siteEvents, $siteEvent);
        //Store the Site into SM
        $sm         = $e->getApplication()->getServiceManager();
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm->setService(self::SM_KEY_SITE, $site);

        //Trigger events on Site
        //Init the Site
        $siteEvents->trigger(SiteEventInterface::EVENT_INIT, $siteEvent);
        //Resolve the Site id
        $siteEvents->trigger(SiteEventInterface::EVENT_RESOLVE, $siteEvent);
        //Get Site config
        $siteEvents->trigger(SiteEventInterface::EVENT_CONFIG, $siteEvent);
        //Load site modules
        $siteEvents->trigger(SiteEventInterface::EVENT_LOAD_MODULES, $siteEvent);
    }
}