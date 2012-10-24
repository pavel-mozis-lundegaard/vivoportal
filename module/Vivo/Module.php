<?php
namespace Vivo;

use Vivo\Module\ModuleManagerFactory;

use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\ServiceManager;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm     = $e->getApplication()->getServiceManager();
        /* @var $sm ServiceManager */
        $config = $sm->get('config');

        //Attach a listener to set up the SiteManager object
        $createSiteListener = $sm->get('create_site_listener');
        $createSiteListener->attach($eventManager);

        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['vivo']['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'storage_factory'   => function(ServiceManager $sm) {
                    $storageFactory = new \Vivo\Storage\Factory();
                    return $storageFactory;
                },
                'module_storage'    => function(ServiceManager $sm) {
                    $config         = $sm->get('config');
                    $storageConfig  = $config['vivo']['modules']['storage'];
                    $storageFactory = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage    = $storageFactory->create($storageConfig);
                    return $storage;
                },
                'module_manager_factory'    => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $ModulePaths            = $config['vivo']['modules']['module_paths'];
                    $moduleStreamName       = $config['vivo']['modules']['stream_name'];
                    $moduleManagerFactory   = new ModuleManagerFactory($ModulePaths, $moduleStreamName);
                    return $moduleManagerFactory;
                },
                'site_resolver'             => function(ServiceManager $sm) {
                    //TODO - configure a proper SiteResolver, the FixedValue resolver is for development only
                    $siteResolver   = new \Vivo\SiteManager\Resolver\FixedValue('abcdefgh12345678');
                    return $siteResolver;
                },
                'site_event'        => function(ServiceManager $sm) {
                    $siteEvent              = new \Vivo\SiteManager\Event\SiteEvent();
                    return $siteEvent;
                },
                'site_manager'      => function(ServiceManager $sm) {
                    $siteEvents             = new \Zend\EventManager\EventManager();
                    $siteEvent              = $sm->get('site_event');
                    $routeParamHost         = 'host';
                    $resolver               = $sm->get('site_resolver');
                    $moduleManagerFactory   = $sm->get('module_manager_factory');
                    //TODO - get list of global modules from somewhere
                    $globalModules          = array('Gvm1');
                    $siteManager            = new \Vivo\SiteManager\SiteManager($siteEvents,
                                                                                $siteEvent,
                                                                                $routeParamHost,
                                                                                $resolver,
                                                                                $moduleManagerFactory,
                                                                                $globalModules);
                    return $siteManager;
                },
                'create_site_listener'  => function(ServiceManager $sm) {
                    $siteManager            = $sm->get('site_manager');
                    $createSiteListener     = new \Vivo\SiteManager\Listener\CreateSiteListener($siteManager);
                    return $createSiteListener;
                },
            ),
        );
    }
}
