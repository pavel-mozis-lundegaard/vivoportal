<?php
namespace Vivo\Module;

use Zend\ModuleManager\ModuleManagerInterface as ZendModuleManagerInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * ModuleManagerInterface
 */
interface ModuleManagerInterface extends ZendModuleManagerInterface
{
    /**
     * Returns application's event manager
     * @return EventManagerInterface
     */
    public function getAppEventManager();
}