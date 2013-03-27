<?php
namespace Vivo\Backend\UI;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * LogonFactory
 */
class LogonFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logon = new Logon($serviceLocator->get('security_manager'));
        return $logon;
    }
}
