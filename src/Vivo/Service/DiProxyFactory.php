<?php
namespace Vivo\Service;

use Vivo\Service\Di\DiProxy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *  This service is a proxy to the Di service
 *
 */
class DiProxyFactory implements FactoryInterface
{
    /**
     * Creates and return Di instance.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di = $serviceLocator->get('di');
        $diProxy = new DiProxy($di, $serviceLocator);
        return $diProxy;
    }
}
