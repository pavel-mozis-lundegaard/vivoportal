<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerAdapterDummyFactory
 * Instantiates the Dummy indexer adapter
 */
class IndexerAdapterDummyFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dummyAdapter   = new \Vivo\Indexer\Adapter\Dummy();
        return $dummyAdapter;
    }
}