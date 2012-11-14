<?php
namespace Vivo\Module;

use Zend\ModuleManager\ModuleManagerInterface as ZendModuleManagerInterface;

/**
 * ModuleManagerInterface
 */
interface ModuleManagerInterface extends ZendModuleManagerInterface
{
    /**
     * Returns application's event manager
     * @return \Zend\EventManager\EventManager
     */
    public function getAppEventManager();
}