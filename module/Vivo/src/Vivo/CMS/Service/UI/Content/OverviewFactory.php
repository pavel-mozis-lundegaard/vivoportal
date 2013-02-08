<?php
namespace Vivo\CMS\Service\UI\Content;

use Vivo\CMS\UI\Content\Overview;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OverviewFactory implements FactoryInterface
{
    /**
     * Create UI Overview object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cms        = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $indexerApi = $serviceLocator->get('Vivo\CMS\Api\Document');
        $siteEvent  = $serviceLocator->get('site_event');
        $service    = new Overview($cms, $indexerApi, $siteEvent);
        return $service;
    }
}
