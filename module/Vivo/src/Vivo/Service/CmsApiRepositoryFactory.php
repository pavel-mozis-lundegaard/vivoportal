<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsApiRepositoryFactory
 */
class CmsApiRepositoryFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository = $serviceLocator->get('repository');
        $api        = new \Vivo\CMS\Api\Repository($repository);
        return $api;
    }
}
