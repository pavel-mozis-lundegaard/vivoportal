<?php
namespace Vivo\CMS\Api;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for Site api service.
 *
 */
class SiteFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Site($serviceLocator->get('Vivo\CMS\Api\CMS'),
                $serviceLocator->get('repository'));
    }
}
