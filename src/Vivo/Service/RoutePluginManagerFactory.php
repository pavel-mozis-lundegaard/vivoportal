<?php
namespace Vivo\Service;

use Zend\Mvc\Service\RoutePluginManagerFactory as ZendRoutePluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoutePluginManagerFactory extends ZendRoutePluginManagerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service    = parent::createService($serviceLocator);
        //TODO - this should be configurable in module.config.php as per https://github.com/zendframework/zf2/pull/3519
        //It does not work that way though
        //Move this configuration to own config key?
        $service->setInvokableClass('Vivo\Router\Hostname', 'Vivo\Router\Hostname');
        $service->setInvokableClass('Vivo\Backend\Hostname', 'Vivo\Backend\Hostname');
        return $service;
    }
}
