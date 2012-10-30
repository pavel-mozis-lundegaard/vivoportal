<?php
namespace Vivo\SiteManager\Listener;

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
     * CMS object
     * @var
     */
    protected $cms;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @throws \Vivo\SiteManager\Exception\ConfigException
     * @return void
     */
    public function onConfig(SiteEventInterface $e)
    {
        $host = $e->getHost();
        if (!$host) {
            throw new Exception\ConfigException(sprintf('%s: Host not set.', __METHOD__));
        }
        //TODO - load Site Entity from repository using the siteId and get site configuration and module names from there
        /*
        $siteModel  = $this->cms->getSiteByHost($host);
        $siteConfig = $this->cms->getSiteConfig($siteModel);
        */
        $siteConfig     = array(
            'site_config_opt_1'     => 'foo',
            'site_config_opt_2'     => 'bar',
            'config_item1'          => 'configured by site',
            'modules'               => array(
                'vm1', 'vm2',
            ),
        );
        $e->setSiteConfig($siteConfig);
    }
}