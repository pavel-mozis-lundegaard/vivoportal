<?php
namespace Vivo\Site\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Vivo\Site\Event\SiteEventInterface;

/**
 * CreateSiteListener
 */
class CreateSiteListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
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
     * Listen to the "dispatch" event, create a new Site and store it in the SM
     * @param  MvcEvent $e
     * @return void
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        //TODO - Get siteEvent and siteEvents from SM?
        $siteEvent  = new \Vivo\Site\Event\SiteEvent();
        $siteEvents = new \Zend\EventManager\EventManager();
        $site       = new \Vivo\Site\Site($routeMatch, $siteEvents, $siteEvent);
        $sm         = $e->getApplication()->getServiceManager();
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm->setService('site', $site);
        //Init the Site
        $site->init();
        //Trigger the bootstrap event
        $siteEvents->trigger(SiteEventInterface::EVENT_BOOTSTRAP, $siteEvent);
    }
}