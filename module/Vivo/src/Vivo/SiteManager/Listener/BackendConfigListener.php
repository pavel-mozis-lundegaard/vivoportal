<?php
namespace Vivo\SiteManager\Listener;

use Vivo\CMS\Api\Site as SiteApi;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * BackendConfigListener
 */
class BackendConfigListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_CONFIG, array($this, 'onConfig'));
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
     * Listen to "config" event, if Backend ctrl is starting get Backend configuration and store it into the SiteEvent
     * @param SiteEventInterface $e
     * @return void
     */
    public function onConfig(SiteEventInterface $e)
    {
        $config = include __DIR__ . "/../../../../config/backend.config.php";
        $e->setBackendConfig($config);
    }
}
