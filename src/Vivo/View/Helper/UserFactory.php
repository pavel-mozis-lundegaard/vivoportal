<?php
namespace Vivo\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * UserFactory
 */
class UserFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        /** @var $securityManager \Vivo\CMS\Security\Manager\AbstractManager */
        $securityManager    = $sm->get('security_manager');
        $userPrincipal      = $securityManager->getUserPrincipal();
        //TODO - provide localized messages in options
        $options            = array();
        $helper = new User($userPrincipal, $options);
        return $helper;
    }
}
