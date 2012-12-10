<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * UuidConvertorFactory
 */
class UuidConvertorFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $indexer        = $serviceLocator->get('indexer');
        $uuidConvertor  =  new \Vivo\Repository\UuidConvertor\UuidConvertor($indexer);
        return $uuidConvertor;
    }
}
