<?php
namespace Vivo\LookupData;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * LookupDataManagerFactory
 */
class LookupDataManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $lookupDataManager  = new LookupDataManager($serviceLocator);
        return $lookupDataManager;
    }
}
