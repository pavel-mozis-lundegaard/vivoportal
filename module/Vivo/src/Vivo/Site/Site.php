<?php
namespace Vivo\Site;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Mvc\Router\RouteMatch;
use Vivo\Site\Event\SiteEventInterface;

/**
 * Site
 */
class Site implements SiteInterface,
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
     * Site ID
     * @var string
     */
    protected $siteId;

    /**
     * RouteMatch object
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * Constructor
     * @param \Zend\Mvc\Router\RouteMatch $routeMatch
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param Event\SiteEventInterface $siteEvent
     */
    public function __construct(RouteMatch $routeMatch, EventManagerInterface $events, SiteEventInterface $siteEvent)
    {
        $this->routeMatch   = $routeMatch;
        $this->events       = $events;
        $this->siteEvent    = $siteEvent;
    }

    /**
     * Init the Site
     */
    public function init()
    {
        $this->siteEvent->setTarget($this);
        $this->events->trigger(SiteEventInterface::EVENT_INIT, $this->siteEvent);
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
     * Returns the Site ID
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }
}