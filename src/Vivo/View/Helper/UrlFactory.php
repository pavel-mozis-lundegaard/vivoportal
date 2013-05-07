<?php
namespace Vivo\View\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * UrlFactory
 */
class UrlFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm             = $serviceLocator->getServiceLocator();
        /** @var $application \Zend\Mvc\Application */
        $application    = $sm->get('application');
        $mvcEvent       = $application->getMvcEvent();
        $routeMatch     = $mvcEvent->getRouteMatch();
        $helper         = new Url();
        $routerService  = \Zend\Console\Console::isConsole() ? 'HttpRouter' : 'Router';
        $router         = $sm->get($routerService);
        $helper->setRouter($router);
        if ($routeMatch instanceof \Zend\Mvc\Router\RouteMatch) {
            $helper->setRouteMatch($routeMatch);
        }
        return $helper;
    }
}
