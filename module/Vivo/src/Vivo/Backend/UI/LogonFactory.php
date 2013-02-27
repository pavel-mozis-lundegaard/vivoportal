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
        $sm = $serviceLocator->get('service_manager');
        $alert = $sm->get('Vivo\UI\Alert');

        $logon = new Logon($sm->get('security_manager'));
        $logon->setAlert($alert);

        return $logon;
    }
}
