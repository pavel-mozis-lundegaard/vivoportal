<?php
namespace Vivo\Module;

use Zend\ModuleManager\ModuleManager as ZendModuleManager;
use Zend\EventManager\EventManager;

/**
 * ModuleManager
 * Vivo Module manager
 */
class ModuleManager extends ZendModuleManager implements ModuleManagerInterface
{
    /**
     * Application's event manager
     * @var EventManager
     */
    protected $appEvents;

    /**
     * Sets the application's event manager
     * @param \Zend\EventManager\EventManager $appEvents
     */
    public function setAppEventManager(EventManager $appEvents)
    {
        $this->appEvents    = $appEvents;
    }

    /**
     * Returns application's event manager
     * @return \Zend\EventManager\EventManager
     */
    public function getAppEventManager()
    {
        return $this->appEvents;
    }
}