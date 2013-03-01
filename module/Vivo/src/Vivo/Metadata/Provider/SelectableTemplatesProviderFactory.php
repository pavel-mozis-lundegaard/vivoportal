<?php
namespace Vivo\Metadata\Provider;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for CMSFrontController
 */
class SelectableTemplatesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('cms_config');
        return new SelectableTemplatesProvider($config['templates']);
    }
}
