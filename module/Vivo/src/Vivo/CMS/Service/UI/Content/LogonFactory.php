<?php
namespace Vivo\CMS\Service\UI\Content;

use Vivo\CMS\UI\Content\Layout;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LogonFactory implements FactoryInterface
{
    /**
     * Create UI Page object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $securityManager    = $serviceLocator->get('security_manager');
        $request            = $serviceLocator->get('request');
        $logon  = new \Vivo\CMS\UI\Content\Logon($securityManager, $request);
        return $logon;
    }
}
