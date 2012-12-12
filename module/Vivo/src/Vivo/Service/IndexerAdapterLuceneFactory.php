<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerAdapterLuceneFactory
 */
class IndexerAdapterLuceneFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $index                  = $serviceLocator->get('lucene');
        $adapter                = new \Vivo\Indexer\Adapter\Lucene($index);
        return $adapter;
    }
}
