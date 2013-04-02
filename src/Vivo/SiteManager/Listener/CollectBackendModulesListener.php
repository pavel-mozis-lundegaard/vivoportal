<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * CollectBackendModulesListener
 */
class CollectBackendModulesListener implements ListenerAggregateInterface
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
        $backendConfig  = $e->getBackendConfig();
        if ($backendConfig && isset($backendConfig['modules'])) {
            //Add modules required by backend to the module stack
            $currentModules = $e->getModules();
            $backendModules = $backendConfig['modules'];
            foreach ($backendModules as $moduleName => $moduleConfig) {
                if ($moduleConfig['enabled']) {
                    $currentModules[]   = $moduleName;
                }
            }
            $currentModules = array_unique($currentModules);
            $e->setModules($currentModules);
        }
    }
}
