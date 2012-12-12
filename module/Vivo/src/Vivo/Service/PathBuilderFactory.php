<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * PathBuilderFactory
 */
class PathBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pathBuilder    = new \Vivo\Storage\PathBuilder\PathBuilder('/');
        return $pathBuilder;
    }
}
