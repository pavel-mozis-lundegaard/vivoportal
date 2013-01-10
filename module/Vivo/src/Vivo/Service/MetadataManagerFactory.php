<?php
namespace Vivo\Service;

use Vivo\Metadata\MetadataManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MetadataManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Vivo\Metadata\Metadata\MetadataManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resource = $serviceLocator->get('module_resource_manager');
        $resolver = $serviceLocator->get('module_name_resolver');

        $manager = new MetadataManager($serviceLocator, $resource, $resolver);

        return $manager;
    }
}
