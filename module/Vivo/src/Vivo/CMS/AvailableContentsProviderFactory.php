<?php
namespace Vivo\CMS;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AvailableContentsProvider factory
 */
class AvailableContentsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $instance = new AvailableContentsProvider();
        $config = $serviceLocator->get('cms_config');
        $instance->setConfig($config['contents']);
        return $instance;
    }
}
