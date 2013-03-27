<?php
namespace Vivo\Backend;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for ModuleResolver
 */
class ModuleResolverFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleResolver = new ModuleResolver();
        $cmsConfig = $serviceLocator->get('cms_config');
        //TODO rename key 'plugins' to 'modules'
        $moduleResolver->setConfig($cmsConfig['backend']['plugins']);
        return $moduleResolver;
    }
}
