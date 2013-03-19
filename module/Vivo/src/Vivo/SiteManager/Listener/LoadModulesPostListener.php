<?php
namespace Vivo\SiteManager\Listener;

use Vivo\CMS\RefInt\Listener;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\Repository\EventInterface as RepositoryEventInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * LoadModulesPostListener
 * Generic listener for the load modules post event
 */
class LoadModulesPostListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Service Locator
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator   = $serviceLocator;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_LOAD_MODULES_POST,
                                             array($this, 'onLoadModulesPost'));
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
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModulesPost(SiteEventInterface $e)
    {
        //Configure RefInt listener
        if ($e->getSite()) {
            //Site exists, hook-up the listener
            /** @var $listener Listener */
            $listener   = $this->serviceLocator->get('ref_int_listener');
            /** @var $repoEvents EventManagerInterface */
            $repoEvents = $this->serviceLocator->get('repository_events');
            $repoEvents->attach(
                RepositoryEventInterface::EVENT_SERIALIZE_PRE, array($listener, 'onRepositorySerializePre'));
            $repoEvents->attach(
                RepositoryEventInterface::EVENT_UNSERIALIZE_POST, array($listener, 'onRepositoryUnSerializePost'));
        }
    }
}