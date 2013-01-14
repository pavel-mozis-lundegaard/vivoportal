<?php
namespace Vivo\Service\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for ResourceFrontController
 */
class ResourceFrontControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $controller = new \Vivo\Controller\ResourceFrontController();
        $controller->setCMS($sm->get('cms'));
        $controller->setResourceManager($sm->get('module_resource_manager'));
        $controller->setSiteEvent($sm->get('site_event'));
        return $controller;
    }
}
