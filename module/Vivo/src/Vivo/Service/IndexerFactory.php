<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerFactory
 */
class IndexerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
//        $adapter                = $serviceLocator->get('indexer_adapter_lucene');
//        $indexer                = new \Vivo\Indexer\Indexer($adapter);
        $indexer                = new \Vivo\Indexer\Dummy();
        return $indexer;
    }
}
