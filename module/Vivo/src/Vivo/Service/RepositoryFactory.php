<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * RepositoryFactory
 */
class RepositoryFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storageConfig          = array(
            'class'     => 'Vivo\Storage\LocalFileSystemStorage',
            'options'   => array(
                'root'          => __DIR__ . '/../../../../../data/repository',
                'path_builder'  => $serviceLocator->get('path_builder'),
            ),
        );
        $storageFactory         = $serviceLocator->get('storage_factory');
        /* @var $storageFactory \Vivo\Storage\Factory */
        $storage                = $storageFactory->create($storageConfig);
        $serializer             = new \Vivo\Serializer\Adapter\Entity();
        $watcher                = new \Vivo\Repository\Watcher();
        $uuidGenerator          = $serviceLocator->get('uuid_generator');
        $ioUtil                 = $serviceLocator->get('io_util');
        //TODO - supply a real cache
        $repository             = new \Vivo\Repository\Repository($storage,
            null,
            $serializer,
            $watcher,
            $uuidGenerator,
            $ioUtil);
        return $repository;
    }
}
