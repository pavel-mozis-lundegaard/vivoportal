<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\SiteManagerInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * RunSiteManagerListener
 * Sets-up a listener for MVC Route event to bootstrap the SiteManager object and prepare the Site
 */
class RunSiteManagerListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * SiteManager
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * Constructor
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager  = $siteManager;
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
        $routeMatch     = $e->getRouteMatch();
        $this->siteManager->setRouteMatch($routeMatch);
        $this->siteManager->bootstrap();
        $this->siteManager->prepareSite();
    }
}