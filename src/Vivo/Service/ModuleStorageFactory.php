<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ModuleStorageFactory
 */
class ModuleStorageFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        if (!isset($config['modules']['storage'])) {
            throw new Exception\ConfigException(sprintf("%s: Module storage configuration missing", __METHOD__));
        }
        $storageConfig  = $config['modules']['storage'];
        $storageConfig['options']['path_builder']   = $serviceLocator->get('path_builder');
        /* @var $storageFactory \Vivo\Storage\Factory */
        $storageFactory = $serviceLocator->get('storage_factory');
        $storage        = $storageFactory->create($storageConfig);
        return $storage;
    }
}