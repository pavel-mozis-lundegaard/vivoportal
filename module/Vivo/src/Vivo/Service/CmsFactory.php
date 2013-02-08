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
        $qb                     = $serviceLocator->get('indexer_query_builder');
        $uuidConvertor          = $serviceLocator->get('uuid_convertor');
        $uuidGenerator          = $serviceLocator->get('uuid_generator');
        $pathBuilder            = $serviceLocator->get('path_builder');
        $cms                    = new \Vivo\CMS\Api\CMS($repository, $qb, $uuidConvertor, $uuidGenerator, $pathBuilder);
        return $cms;
    }
}
