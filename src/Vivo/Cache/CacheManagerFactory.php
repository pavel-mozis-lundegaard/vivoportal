<?php
namespace Vivo\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CacheManagerFactory
 */
class CacheManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        if (isset($config['cache_manager'])) {
            $options    = $config['cache_manager'];
        } else {
            $options    = array();
        }
        $cacheAbstractFactory   = new CacheAbstractFactory($options);
        $service                = new CacheManager();
        $service->addAbstractFactory($cacheAbstractFactory);
        return $service;
    }
}
