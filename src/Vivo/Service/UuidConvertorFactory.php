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
        $indexer            = $serviceLocator->get('indexer');
        $repositoryEvents   = $serviceLocator->get('repository_events');
        $uuidConvertor      = new \Vivo\CMS\UuidConvertor\UuidConvertor($indexer, $repositoryEvents);
        return $uuidConvertor;
    }
}
