<?php
namespace Vivo\Module;

use Zend\ModuleManager\ModuleManager as ZendModuleManager;
use Zend\EventManager\EventManagerInterface;

/**
 * ModuleManager
 * Vivo Module manager
 */
class ModuleManager extends ZendModuleManager implements ModuleManagerInterface
{
    /**
     * Application's event manager
     * @var EventManagerInterface
     */
    protected $appEvents;

    /**
     * Sets the application's event manager
     * @param EventManagerInterface $appEvents
     */
    public function setAppEventManager(EventManagerInterface $appEvents)
    {
        $this->appEvents    = $appEvents;
    }

    /**
     * Returns application's event manager
     * @return EventManagerInterface
     */
    public function getAppEventManager()
    {
        return $this->appEvents;
    }
}