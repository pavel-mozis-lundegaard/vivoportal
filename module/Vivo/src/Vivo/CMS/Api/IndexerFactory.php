<?php
namespace Vivo\CMS\Api;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerFactory
 * Indexer API Factory
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
        $indexer        = $serviceLocator->get('indexer');
        $indexerHelper  = $serviceLocator->get('indexer_helper');
        $queryParser    = $serviceLocator->get('indexer_query_parser');
        $queryBuilder   = $serviceLocator->get('indexer_query_builder');
        $repository     = $serviceLocator->get('repository');
        $documentApi    = $serviceLocator->get('document_api');
        $pathBuilder    = $serviceLocator->get('path_builder');
        $service        = new Indexer($indexer, $indexerHelper, $queryParser, $queryBuilder, $repository, $documentApi,
                                      $pathBuilder);
        return $service;
    }
}
