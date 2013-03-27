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
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storageConfig  = array(
            'class'     => 'Vivo\Storage\LocalFileSystemStorage',
            'options'   => array(
                'root'          => __DIR__ . '/../../../../../vmodule',
                'path_builder'  => $serviceLocator->get('path_builder'),
            ),
        );
        /* @var $storageFactory \Vivo\Storage\Factory */
        $storageFactory = $serviceLocator->get('storage_factory');
        $storage        = $storageFactory->create($storageConfig);
        return $storage;
    }
}