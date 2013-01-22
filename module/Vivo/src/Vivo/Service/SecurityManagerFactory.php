<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Security manager factory.
 */
class SecurityManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return Manager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = new \Vivo\CMS\Security\DB\Manager($serviceLocator->get('session_manager'));
        $domain = $serviceLocator->get('site_event')->getSite()->getDomain();
        $sm->setDomain($domain);
        return $sm;
    }
}
