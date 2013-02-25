<?php
namespace Vivo\SiteManager\Listener;

use Vivo\CMS\Api\Site as SiteApi;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * SiteConfigListener
 */
class SiteConfigListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var SiteApi
     */
    protected $siteApi;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\Site $siteApi
     */
    public function __construct(SiteApi $siteApi)
    {
        $this->siteApi = $siteApi;
    }

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
     * Listen to "config" event, get Site configuration and store it into the SiteEvent
     * @param SiteEventInterface $e
     * @return void
     */
    public function onConfig(SiteEventInterface $e)
    {
        $siteModel  = $e->getSite();
        if ($siteModel) {
            $siteConfig = $this->siteApi->getSiteConfig($siteModel);
            $e->setSiteConfig($siteConfig);
            $e->stopPropagation(true);
        }
    }
}