<?php
namespace Vivo\CMS\Api\Manager;

use Vivo\CMS\Api\Manager\Manager;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Manager($serviceLocator->get('Vivo\CMS\Api\CMS'),
                $serviceLocator->get('repository'));
    }
}
