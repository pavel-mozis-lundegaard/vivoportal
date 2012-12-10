<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsApiModuleFactory
 */
class CmsApiModuleFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $installManager = $serviceLocator->get('module_install_manager');
        $api            = new \Vivo\CMS\Api\Module($installManager);
        return $api;
    }
}
