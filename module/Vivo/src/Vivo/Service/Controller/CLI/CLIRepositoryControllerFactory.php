<?php
namespace Vivo\Service\Controller\CLI;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for CLI\Repository controller.
 */
class CLIRepositoryControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm             = $serviceLocator->getServiceLocator();
        $apiRepository  = $sm->get('cms_api_repository');
        $siteEvent      = $sm->get('site_event');
        $controller     = new \Vivo\Controller\CLI\RepositoryController($apiRepository, $siteEvent);
        return $controller;
    }
}
