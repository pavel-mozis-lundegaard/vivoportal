<?php
namespace Vivo\CMS\Api;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsApiDocumentFactory
 */
class DocumentFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cms                = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $repository         = $serviceLocator->get('repository');
        $pathBuilder        = $serviceLocator->get('path_builder');
        $workflowFactory    = $serviceLocator->get('workflow_factory');
        $uuidGenerator      = $serviceLocator->get('uuid_generator');
        $api                = new Document($cms, $repository, $pathBuilder, $workflowFactory, $uuidGenerator);
        return $api;
    }
}
