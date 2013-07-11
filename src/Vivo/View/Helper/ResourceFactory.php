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
        $sm                     = $serviceLocator->getServiceLocator();
        /** @var $application \Zend\Mvc\Application */
        $application            = $sm->get('application');
        $mvcEvent               = $application->getMvcEvent();
        $routeMatch             = $mvcEvent->getRouteMatch();
        $routeName              = $routeMatch->getMatchedRouteName();
        $cmsApi                 = $sm->get('Vivo\CMS\Api\CMS');
        $moduleResourceManager  = $sm->get('module_resource_manager');
        $resourceHelperOptions  = array(
            'vivo_resource_path'    => realpath(__DIR__ . '/../../../../resource/'),
        );
        $helper                 = new Resource($cmsApi, $moduleResourceManager, $routeName, $resourceHelperOptions);
        return $helper;
    }
}
