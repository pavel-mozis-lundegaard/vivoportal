<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\CMS\CMS;

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
     * CMS object
     * @var CMS
     */
    protected $cms;

    /**
     * Constructor
     * @param \Vivo\CMS\CMS $cms
     */
    public function __construct(CMS $cms)
    {
        $this->cms  = $cms;
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
        $siteModel  = $e->getSiteModel();
        if ($siteModel) {
            $siteConfig = $this->cms->getSiteConfig($siteModel);
            $e->setSiteConfig($siteConfig);
            $e->stopPropagation(true);
        }
    }
}