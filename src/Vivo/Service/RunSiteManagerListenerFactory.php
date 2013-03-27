<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * RunSiteManagerListenerFactory
 */
class RunSiteManagerListenerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $siteManager            = $serviceLocator->get('site_manager');
        $runSiteManagerListener = new \Vivo\SiteManager\Listener\RunSiteManagerListener($siteManager);
        return $runSiteManagerListener;
    }
}
