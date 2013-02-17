<?php
namespace Vivo\SiteManager\Listener;

use Vivo\CMS\Api\Site as SiteApi;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * SiteModelLoadListener
 */
class SiteModelLoadListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Route parameter name containing the host name
     * @var string
     */
    protected $routeParamHost;

    /**
     * Site API
     * @var SiteApi
     */
    protected $siteApi;

    /**
     * Constructor
     * @param $routeParamHost
     * @param \Vivo\CMS\Api\Site $siteApi
     */
    public function __construct($routeParamHost, SiteApi $siteApi)
    {
        $this->routeParamHost   = $routeParamHost;
        $this->siteApi          = $siteApi;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_SITE_MODEL_LOAD, array($this, 'onSiteModelLoad'));
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
     * Listen to "siteModelLoad" event, get host name from RouteMatch, get site model from CMS and store it into Site Event
     * @param SiteEventInterface $e
     * @param \Vivo\SiteManager\Event\SiteEventInterface $e
     * @return void
     */
    public function onSiteModelLoad(SiteEventInterface $e)
    {
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch) {
            $host  = $routeMatch->getParam($this->routeParamHost);
            if ($host) {
                $siteModel  = $this->siteApi->getSiteByHost($host);
                $e->setHost($host);
                $e->setSite($siteModel);
                $e->stopPropagation(true);
            }
        }
    }
}
