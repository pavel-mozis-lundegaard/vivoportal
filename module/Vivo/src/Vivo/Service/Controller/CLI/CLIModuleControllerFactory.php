<?php
namespace Vivo\Service\Controller\CLI;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for CLI\Module controller.
 */
class CLIModuleControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm                     = $serviceLocator->getServiceLocator();
        $moduleStorageManager   = $sm->get('module_storage_manager');
        $remoteModule           = $sm->get('remote_module');
        $repository             = $sm->get('repository');
        $moduleApi              = $sm->get('module_api');
        $controller             = new \Vivo\Controller\CLI\ModuleController($moduleStorageManager, $remoteModule,
                                                                            $repository, $moduleApi);
        return $controller;
    }
}
