<?php
namespace Vivo\Service\Controller\CLI;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for CLI\Cms controller.
 */
class CLICmsControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm             = $serviceLocator->getServiceLocator();
        $cms            = $sm->get('cms');
        $siteEvent      = $sm->get('site_event');
        $repository     = $sm->get('repository');
        $uuidGenerator  = $sm->get('uuid_generator');
        $controller     = new \Vivo\Controller\CLI\CmsController($cms, $siteEvent, $repository, $uuidGenerator);
        return $controller;
    }
}
