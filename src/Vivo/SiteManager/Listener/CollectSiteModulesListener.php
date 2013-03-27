<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * CollectSiteModulesListener
 */
class CollectSiteModulesListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners    = array();

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_COLLECT_MODULES, array($this, 'onCollectModules'));
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
     * Listen to "collect_modules" event, merge list of global modules with the site modules and add modules from dependencies
     * @param SiteEventInterface $e
     * @return void
     */
    public function onCollectModules(SiteEventInterface $e)
    {
        $siteConfig = $e->getSiteConfig();
        if ($siteConfig && isset($siteConfig['modules']['site_modules'])) {
            //Add modules required by site to the module stack
            $currentModules = $e->getModules();
            $siteModules    = $siteConfig['modules']['site_modules'];
            foreach ($siteModules as $moduleName => $moduleConfig) {
                if ($moduleConfig['enabled']) {
                    $currentModules[]   = $moduleName;
                }
            }
            $currentModules = array_unique($currentModules);
            $e->setModules($currentModules);
        }
    }
}
