<?php
namespace Vivo\Site\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Router\RouteMatch;
use Vivo\Site\Site;
use Vivo\Site\Event\SiteEventInterface;
use Vivo\Site\Exception;
use Vivo\Site\Resolver\ResolverInterface;

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
     * Site alias resolver
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
     * Listen to "resolve" event, get site alias from RouteMatch, resolve it to site id and set site alias and id to Site
     * @param SiteEventInterface $e
     * @throws \Vivo\Site\Exception\ResolveException
     * @throws \Vivo\Site\Exception\InvalidArgumentException
     * @return void
     */
    public function onResolve(SiteEventInterface $e)
    {
        $routeMatch = $e->getParam('route_match');
        if (!$routeMatch) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Parameter 'route_match' missing in SiteEvent",
                                                                 __METHOD__));
        }
        /* @var $routeMatch RouteMatch */
        $siteAlias  = $routeMatch->getParam($this->routeParamHost);
        if ($siteAlias) {
            $siteId = $this->resolver->resolve($siteAlias);
            if ($siteId) {
                //Site has been resolved
                $site   = $e->getTarget();
                /* @var $site Site */
                $site->setSiteId($siteId);
                $site->setSiteAlias($siteAlias);
                $e->stopPropagation(true);
            }
        }
    }
}