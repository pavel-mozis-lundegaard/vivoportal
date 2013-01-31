<?php
namespace Vivo\CMS\Service\UI\Content;

use Vivo\CMS\UI\Content\Overview;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OverviewFactory implements FactoryInterface
{
    /**
     * Create UI Page object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Overview($serviceLocator->get('Vivo\CMS\Api\CMS'), $serviceLocator->get('site_event'));
    }
}
