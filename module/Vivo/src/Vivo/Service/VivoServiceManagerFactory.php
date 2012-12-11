<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class VivoServiceManagerFactory implements FactoryInterface
{
    /**
     * Creates and return Di instance.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //TODO configure using values from [vivo][service_manager] key in config
        //TODO configure by loaded modules
        $sm = new ServiceManager();
        $sm->addPeeringServiceManager($serviceLocator);
        return $sm;
    }
}
