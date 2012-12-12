<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * StorageUtilFactory
 */
class StorageUtilFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $ioUtil         = $serviceLocator->get('io_util');
        $storageUtil    = new \Vivo\Storage\StorageUtil($ioUtil);
        return $storageUtil;
    }
}
