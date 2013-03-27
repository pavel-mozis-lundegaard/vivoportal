<?php
namespace Vivo\UI;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AlertFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Alert
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Alert($serviceLocator->get('session_manager'));
    }
}
