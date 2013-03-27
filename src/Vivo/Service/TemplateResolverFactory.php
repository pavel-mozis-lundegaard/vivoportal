<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsFactory
 */
class TemplateResolverFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Vivo\View\Resolver\TemplateResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('cms_config');
        return new \Vivo\View\Resolver\TemplateResolver(
                $serviceLocator->get('module_resource_manager'),
                new \Vivo\Util\Path\PathParser(),
                $config['templates']);
    }
}
