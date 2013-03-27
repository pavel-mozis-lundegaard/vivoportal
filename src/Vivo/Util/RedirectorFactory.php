<?php
namespace Vivo\Util;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Redirector factory
 */
class RedirectorFactory implements FactoryInterface
{
    /**
     * Create Redirector
     * @param ServiceLocatorInterface $serviceLocator
     * @return Redirector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $redirector = new Redirector($serviceLocator->get('request'),
                $serviceLocator->get('response'));
        $redirector->setSharedManager($serviceLocator->get('shared_event_manager'));
        return $redirector;
    }
}
