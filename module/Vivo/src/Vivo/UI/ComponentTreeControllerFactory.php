<?php
namespace Vivo\UI;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ComponentTreeControllerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Alert
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ComponentTreeController(
                $serviceLocator->get('session_manager'),
                $serviceLocator->get('request')
                );
    }
}
