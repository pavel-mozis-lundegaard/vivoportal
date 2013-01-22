<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * RepositoryApiFactory
 */
class RepositoryApiFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository             = $serviceLocator->get('repository');
        $repositoryApi          = new \Vivo\CMS\Api\Repository($repository);
        return $repositoryApi;
    }
}
