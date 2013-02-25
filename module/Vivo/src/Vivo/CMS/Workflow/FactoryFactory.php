<?php
namespace Vivo\CMS\Workflow;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * FactoryFactory
 * Workflow Factory Factory
 */
class FactoryFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service        = new Factory($serviceLocator);
        return $service;
    }
}
