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
     * @param \Vivo\Site\Resolver\ResolverInterface $resolver
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
     * Listen to "resolve" event, get site alias from RouteMatch, resolve it to site id and set site alias and id to SiteManager
     * @param SiteEventInterface $e
     * @throws \Vivo\SiteManager\Exception\ResolveException
     * @throws \Vivo\SiteManager\Exception\InvalidArgumentException
     * @return void
     */
    public function onResolve(SiteEventInterface $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Parameter 'route_match' missing in SiteEvent",
                                                                 __METHOD__));
        }
        $siteAlias  = $routeMatch->getParam($this->routeParamHost);
        if ($siteAlias) {
            $siteId = $this->resolver->resolve($siteAlias);
            if ($siteId) {
                //SiteManager has been resolved
                $site   = $e->getTarget();
                /* @var $site SiteManager */
                $site->setSiteId($siteId);
                $site->setSiteAlias($siteAlias);
                $e->stopPropagation(true);
            }
        }
    }
}