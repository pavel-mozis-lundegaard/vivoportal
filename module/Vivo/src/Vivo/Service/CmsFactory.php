<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsFactory
 */
class CmsFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository             = $serviceLocator->get('repository');
        $indexer                = $serviceLocator->get('indexer');
        $indexerHelper          = $serviceLocator->get('indexer_helper');
        $qb                     = $serviceLocator->get('indexer_query_builder');
        $cms                    = new \Vivo\CMS\Api\CMS($repository, $indexer, $indexerHelper, $qb);
        return $cms;
    }
}
