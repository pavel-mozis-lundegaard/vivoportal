<?php
namespace Vivo;

use Vivo\CMS\ComponentFactory;
use Vivo\CMS\ComponentResolver;
use Vivo\Module\ModuleManagerFactory;
use Vivo\View\Helper\Action;
use Vivo\View\Strategy\PhtmlRenderingStrategy;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\ServiceManager;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
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

        $eventManager->attach('render', array ($this, 'registerUIRenderingStrategies'), 100);
        $eventManager->attach('render', array ($this, 'registerViewHelpers'), 100);
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


    /**
     * Register rendering strategy fo Vivo UI.
     *
     * @param unknown_type $e
     */
    public function registerUIRenderingStrategies($e)
    {
        $app          = $e->getTarget();
        $locator      = $app->getServiceManager();
        $view         = $locator->get('Zend\View\View');
        $phtmlRenderingStrategy = $locator->get('Vivo\View\Strategy\PhtmlRenderingStrategy');
        $view->getEventManager()->attach($phtmlRenderingStrategy, 100);
    }

    public function registerViewHelpers($e) {
        $app          = $e->getTarget();
        $serviceLocator      = $app->getServiceManager();
        $plugins      = $serviceLocator->get('view_helper_manager');
        $plugins->setFactory('action', function($sm) use($serviceLocator) {
            $helper = new Action($sm->get('url'));
            return $helper;
        });
    }


    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ioUtil'            => function(ServiceManager $sm) {
                    $ioUtil     = new \Vivo\IO\IOUtil();
                    return $ioUtil;
                },
                'storage_util'      => function(ServiceManager $sm) {
                    $ioUtil         = $sm->get('ioUtil');
                    $storageUtil    = new \Vivo\Storage\StorageUtil($ioUtil);
                    return $storageUtil;
                },
                'storage_factory'   => function(ServiceManager $sm) {
                    $storageFactory = new \Vivo\Storage\Factory();
                    return $storageFactory;
                },
                'path_builder'      => function(ServiceManager $sm) {
                    $pathBuilder    = new \Vivo\Storage\PathBuilder\PathBuilder('/');
                    return $pathBuilder;
                },
                'module_storage'    => function(ServiceManager $sm) {
                    $storageConfig  = array(
                        'class'     => 'Vivo\Storage\LocalFileSystemStorage',
                        'options'   => array(
                            'root'          => __DIR__ . '/../../vmodule',
                            'path_builder'  => $sm->get('path_builder'),
                        ),
                    );
                    $storageFactory = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage    = $storageFactory->create($storageConfig);
                    return $storage;
                },
                'remote_module'             => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $descriptorName         = $config['vivo']['modules']['descriptor_name'];
                    $storageFactory         = $sm->get('storage_factory');
                    $pathBuilder            = $sm->get('path_builder');
                    $remoteModule           = new \Vivo\Module\StorageManager\RemoteModule($storageFactory,
                                                                                           $descriptorName,
                                                                                           $pathBuilder);
                    return $remoteModule;
                },
                'module_storage_manager'    => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $modulePaths            = $config['vivo']['modules']['module_paths'];
                    $descriptorName         = $config['vivo']['modules']['descriptor_name'];
                    $defaultInstallPath     = $config['vivo']['modules']['default_install_path'];
                    $storage                = $sm->get('module_storage');
                    $storageUtil            = $sm->get('storage_util');
                    $remoteModule           = $sm->get('remote_module');
                    $pathBuilder            = $sm->get('path_builder');
                    $manager    = new \Vivo\Module\StorageManager\StorageManager($storage,
                                                                                 $modulePaths,
                                                                                 $descriptorName,
                                                                                 $defaultInstallPath,
                                                                                 $storageUtil,
                                                                                 $remoteModule,
                                                                                 $pathBuilder);
                    return $manager;
                },
                'module_manager_factory'    => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $modulePaths            = $config['vivo']['modules']['module_paths'];
                    $moduleStreamName       = $config['vivo']['modules']['stream_name'];
                    $moduleManagerFactory   = new ModuleManagerFactory($modulePaths, $moduleStreamName);
                    return $moduleManagerFactory;
                },
                'Vivo\View\Strategy\PhtmlRenderingStrategy' => function(ServiceManager $sm) {
                    $config = $sm->get('config');
                    $resolver = new \Vivo\View\Resolver\TemplateResolver($config['vivo']['templates']);
                    $renderer = new \Vivo\View\Renderer\PhtmlRenderer();
                    $renderer->setResolver($resolver);
                    $renderer->setHelperPluginManager($sm->get('ViewHelperManager'));
                    $strategy = new PhtmlRenderingStrategy($renderer, $resolver);
                    return $strategy;
                },
                'Vivo\CMS\ComponentFactory' => function(ServiceManager $sm) {
                    $di = $sm->get('di');
                    //setup DI with shared instances from Vivo
                    //TODO move di setup somewhere else
                    $di->instanceManager()
                    ->addSharedInstance($sm->get('request'), 'Zend\Http\Request');
                    $di->instanceManager()
                    ->addSharedInstance($sm->get('response'), 'Zend\Http\Response');
                    $di->instanceManager()
                    ->addSharedInstance($sm->get('cms'), 'Vivo\CMS\CMS');

                    $cf = new ComponentFactory($di, $sm->get('cms'), $sm->get('site_event')->getSiteModel());
                    $resolver = new ComponentResolver($sm->get('config'));
                    $cf->setResolver($resolver);
                    return $cf;
                },
                'site_event'        => function(ServiceManager $sm) {
                    $siteEvent              = new \Vivo\SiteManager\Event\SiteEvent();
                    return $siteEvent;
                },
                'site_manager'      => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $coreModules            = $config['vivo']['modules']['core_modules'];
                    $siteEvents             = new \Zend\EventManager\EventManager();
                    $siteEvent              = $sm->get('site_event');
                    $routeParamHost         = 'host';
                    $moduleManagerFactory   = $sm->get('module_manager_factory');
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $cms                    = $sm->get('cms');
                    $moduleResourceManager    = $sm->get('module_resource_manager');
                    $siteManager            = new \Vivo\SiteManager\SiteManager($siteEvents,
                                                                                $siteEvent,
                                                                                $routeParamHost,
                                                                                $moduleManagerFactory,
                                                                                $coreModules,
                                                                                $moduleStorageManager,
                                                                                $cms,
                                                                                $sm,
                                                                                $moduleResourceManager);
                    return $siteManager;
                },
                'create_site_listener'  => function(ServiceManager $sm) {
                    $siteManager            = $sm->get('site_manager');
                    $createSiteListener     = new \Vivo\SiteManager\Listener\CreateSiteListener($siteManager);
                    return $createSiteListener;
                },
                'indexer'                   => function(ServiceManager $sm) {
                    $indexer                = new \Vivo\Indexer\Indexer();
                    return $indexer;
                },
                'repository'                => function(ServiceManager $sm) {
                    $storageConfig          = array(
                        'class'     => 'Vivo\Storage\LocalFileSystemStorage',
                        'options'   => array(
                            'root'          => __DIR__ . '/../../data/repository',
                            'path_builder'  => $sm->get('path_builder'),
                        ),
                    );
                    $storageFactory         = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage                = $storageFactory->create($storageConfig);
                    $indexer                = $sm->get('indexer');
                    $serializer             = new \Vivo\Serializer\Adapter\Entity();
                    //TODO - supply a real cache
                    $repository             = new \Vivo\Repository\Repository($storage, null, $indexer, $serializer);
                    return $repository;
                },
                'cms'                       => function(ServiceManager $sm) {
                    $repository             = $sm->get('repository');
                    $cms                    = new \Vivo\CMS\CMS($repository);
                    return $cms;
                },
                'module_resource_manager'   => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $resourceBase           = $config['vivo']['modules']['resource_base'];
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $pathBuilder            = $sm->get('path_builder');
                    $moduleResourceManager  = new \Vivo\Module\ResourceManager\ResourceManager($moduleStorageManager,
                                                                                               $resourceBase,
                                                                                               $pathBuilder);
                    return $moduleResourceManager;
                },
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories'     => array(
                'CMSFront' => function (ControllerManager $cm) {
                    $fc = new \Vivo\Controller\CMSFrontController();
                    $sm = $cm->getServiceLocator();
                    $fc->setComponentFactory($sm->get('Vivo\CMS\ComponentFactory'));
                    $fc->setTreeUtil($sm->get('Vivo\UI\TreeUtil'));
                    $fc->setCMS($sm->get('cms'));
                    //TODO get site from SiteManager
                    $fc->setSiteEvent($sm->get('site_event'));
                    return $fc;
                },
                'CLI\Module'    => function(ControllerManager $cm) {
                    $sm                     = $cm->getServiceLocator();
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $remoteModule           = $sm->get('remote_module');
                    $controller             = new \Vivo\Controller\CLI\ModuleController($moduleStorageManager, $remoteModule);
                    return $controller;
                },
                'ResourceFront'    => function(ControllerManager $cm) {
                    $sm                     = $cm->getServiceLocator();
                    $controller             = new \Vivo\Controller\ResourceFrontController();
                    $controller->setResourceManager($sm->get('module_resource_manager'));
                    return $controller;
                },
            ),
        );
    }

    public function getConsoleBanner(Console $console){
        return
        "==========================================================\n".
        "    Vivo 2 CLI                                            \n".
        "==========================================================\n"
        ;
    }

    public function getConsoleUsage(Console $console){
        return array('Available commands:',
                array ('indexer', 'Perform operations on indexer..'),
                array ('info','Show informations about CMS instance.'),
                array ('module', 'Manage modules.'),
        );
    }
}
