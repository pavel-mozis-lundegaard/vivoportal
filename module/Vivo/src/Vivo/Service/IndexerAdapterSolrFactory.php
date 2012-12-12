<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerAdapterSolrFactory
 * Instantiates the SolrAdapter
 */
class IndexerAdapterSolrFactory implements FactoryInterface
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
        $idField        = 'vivo_cms_model_entity_path';
        $solrAdapter    = new \Vivo\Indexer\Adapter\Solr($solrService, $idField);
        return $solrAdapter;
    }
}