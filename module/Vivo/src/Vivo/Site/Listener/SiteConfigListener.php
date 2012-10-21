<?php
namespace Vivo\Site\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Vivo\Site\Site;
use Vivo\Site\Event\SiteEventInterface;
use Vivo\Site\Exception;

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
     * Listen to "config" event, get Site configuration and required module names and store it into the Site object
     * @param SiteEventInterface $e
     * @throws \Vivo\Site\Exception\ConfigException
     * @return void
     */
    public function onConfig(SiteEventInterface $e)
    {
        $site   = $e->getTarget();
        /* @var $site Site */
        $siteId = $site->getSiteId();
        if (!$siteId) {
            throw new Exception\ConfigException(sprintf('%s: SiteId not set.', __METHOD__));
        }
        //TODO - load Site Entity from repository using the siteId and get site configuration and module names from there
        $siteConfig     = array(
            'site_config_opt_1'     => 'foo',
            'site_config_opt_2'     => 'bar',
            'config_item1'          => 'configured by site',
        );
        $siteModules    = array(
            'vm1', 'vm2',
        );
        $site->setConfig($siteConfig);
        $site->setModules($siteModules);
    }
}