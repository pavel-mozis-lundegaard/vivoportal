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
     * Site alias resolver
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * Constructor
     * @param \Vivo\Site\Resolver\ResolverInterface $resolver
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
        $siteAlias  = $routeMatch->getParam('site_alias');
        if (!$siteAlias) {
            throw new Exception\ResolveException(sprintf("%s: Parameter 'site_alias' missing in RouteMatch",
                __METHOD__));
        }
        $siteId = $this->resolver->resolve($siteAlias);
        if (!$siteId) {
            throw new Exception\ResolveException(sprintf("%s: Site alias '%s' cannot be resolved to a site id",
                __METHOD__, $siteAlias));
        }
        $site   = $e->getTarget();
        /* @var $site Site */
        $site->setSiteId($siteId);
        $site->setSiteAlias($siteAlias);
    }
}