<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\SiteManager;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\SiteManager\Resolver\ResolverInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Router\RouteMatch;

/**
 * SiteResolveListener
 */
class SiteResolveListener implements ListenerAggregateInterface
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
     * SiteManager alias resolver
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * Constructor
     * @param string $routeParamHost Route param name containing the host name
     * @param ResolverInterface $resolver
     */
    public function __construct($routeParamHost, ResolverInterface $resolver)
    {
        $this->routeParamHost   = $routeParamHost;
        $this->resolver         = $resolver;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_RESOLVE, array($this, 'onResolve'));
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
     * Listen to "resolve" event, get host name from RouteMatch, resolve it to site id and set host name and id to SiteEvent
     * @param SiteEventInterface $e
     * @throws \Vivo\SiteManager\Exception\ResolveException
     * @throws \Vivo\SiteManager\Exception\InvalidArgumentException
     * @return void
     */
    public function onResolve(SiteEventInterface $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new Exception\InvalidArgumentException(sprintf("%s: RouteMatch missing in SiteEvent",
                                                                 __METHOD__));
        }
        $host  = $routeMatch->getParam($this->routeParamHost);
        if ($host) {
            $siteId = $this->resolver->resolve($host);
            if ($siteId) {
                //Site has been resolved
                $e->setSiteId($siteId);
                $e->setHost($host);
                $e->stopPropagation(true);
            }
        }
    }
}