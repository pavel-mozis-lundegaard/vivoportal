<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config as SmConfig;

/**
 * NavigationHelperRegistrar
 * Registers navigation view helpers
 */
class NavigationHelperRegistrar
{
    /**
     * The navigation view helper manager will be registered under this name in the service manager
     */
    const NAVIGATION_HELPER_MANAGER_SERVICE = 'navigation_view_helper_manager';

    /**
     * Registers custom navigation view helpers
     * Also registers the navigation view helper manager with the service manager
     * @param ServiceManager $serviceManger
     */
    public function registerNavigationHelpers(ServiceManager $serviceManger)
    {
        //Register navigation plugin manager in service locator
        $viewHelperManager      = $serviceManger->get('view_helper_manager');
        /** @var $navigationHelper \Zend\View\Helper\Navigation */
        $navigationHelper       = $viewHelperManager->get('navigation');
        $navigationPluginMgr    = $navigationHelper->getPluginManager();
        $serviceManger->setService(self::NAVIGATION_HELPER_MANAGER_SERVICE, $navigationPluginMgr);
        //Add custom navigation view helpers
        $config                 = $serviceManger->get('config');
        if (isset($config['navigation_view_helpers'])) {
            $navigationViewHelperConfig = new SmConfig($config['navigation_view_helpers']);
            $navigationViewHelperConfig->configureServiceManager($navigationPluginMgr);
        }
    }
}
