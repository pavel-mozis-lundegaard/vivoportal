<?php
namespace Vivo\Backend\UI;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for CMSFrontController
 */
class SiteSelectorFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $siteSelector = new SiteSelector(
                $serviceLocator->get('Vivo\CMS\Api\Site'),
                $serviceLocator->get('site_event'));
        return $siteSelector;
    }
}
