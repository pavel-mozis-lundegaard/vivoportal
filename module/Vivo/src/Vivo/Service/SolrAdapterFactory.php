<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * SolrAdapterFactory
 * Instantiates the SolrAdapter
 */
class SolrAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $solrService    = $serviceLocator->get('solr_service');
        //TODO - get name of the unique ID field from config
        $solrAdapter    = new \Vivo\Indexer\Adapter\Solr($solrService, 'id');
        return $solrAdapter;
    }
}