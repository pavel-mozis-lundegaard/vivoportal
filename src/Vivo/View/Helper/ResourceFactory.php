<?php
namespace Vivo\View\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ResourceFactory
 */
class ResourceFactory implements  FactoryInterface
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
        $routeName      = $routeMatch->getMatchedRouteName();

        $cmsApi         = $sm->get('Vivo\CMS\Api\CMS');
        $helper         = new Resource($cmsApi);

        //Define resource routes for Resource view helper
        $resourceRouteMap = array(
            'vivo/cms'          => 'vivo/resource',
            'backend/cms'       => 'backend/resource',
            'backend/modules'   => 'backend/backend_resource',
            'backend/explorer'  => 'backend/backend_resource',
            'backend/other'     => 'backend/backend_resource',
            'backend/default'   => 'backend/backend_resource',
        );

        $resourceRouteName = isset($resourceRouteMap[$routeName]) ? $resourceRouteMap[$routeName] : '';
        $helper->setResourceRouteName($resourceRouteName);
        return $helper;
    }
}
