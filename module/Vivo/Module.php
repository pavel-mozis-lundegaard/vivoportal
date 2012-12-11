<?php
namespace Vivo;

use Vivo\CMS\ComponentFactory;
use Vivo\CMS\ComponentResolver;
use Vivo\Module\ModuleManagerFactory;
use Vivo\Util\Path\PathParser;
use Vivo\View\Helper as ViewHelper;
use Vivo\View\Strategy\PhtmlRenderingStrategy;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Di\Config;
use Zend\Di\Di;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    /**
     * Module bootstrap method.
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm     = $e->getApplication()->getServiceManager();
        /* @var $sm ServiceManager */
        $config = $sm->get('config');

        //Attach a listener to set up the SiteManager object
        $runSiteManagerListener = $sm->get('run_site_manager_listener');
        $runSiteManagerListener->attach($eventManager);

        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['vivo']['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);

        $eventManager->attach('render', array ($this, 'registerUIRenderingStrategies'), 100);
        $eventManager->attach('render', array ($this, 'registerViewHelpers'), 100);

        //TODO attach to SiteEvent
        $eventManager->attach('route', array ($this, 'initializeVivoServiceManager'), 100000000);
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
     * Initialize vivo service manager.
     *
     * This method register factory for vivo_service_manager to the application service manager.
     * The factory is not registered in service manager configuration to avoid instatniate it until
     * site and modules are loaded.
     *
     * @param MvcEvent $e
     */
    public function initializeVivoServiceManager(MvcEvent $e)
    {
        $app          = $e->getTarget();
        $sm      = $app->getServiceManager();
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm->setFactory('vivo_service_manager', 'Vivo\Service\VivoServiceManagerFactory');
        $vsm = $sm->get('vivo_service_manager');
        $di = $sm->get('di');
        $config = $sm->get('config');
        $di->configure(new Config($config['vivo']['di']));
        $vsm->setFactory('di_proxy', 'Vivo\Service\DiProxyFactory');
    }
    /**
     * Register rendering strategy fo Vivo UI.
     * @param MvcEvent $e
     */
    public function registerUIRenderingStrategies(MvcEvent $e)
    {
        $app          = $e->getTarget();
        $locator      = $app->getServiceManager();
        $view         = $locator->get('Zend\View\View');
        $phtmlRenderingStrategy = $locator->get('Vivo\View\Strategy\PhtmlRenderingStrategy');
        $view->getEventManager()->attach($phtmlRenderingStrategy, 100);
    }

    /**
     * Registers view helpers to the view helper manager.
     * @param MvcEvent $e
     */
    public function registerViewHelpers($e) {
        $app          = $e->getTarget();
        $serviceLocator      = $app->getServiceManager();
        $plugins      = $serviceLocator->get('view_helper_manager');
        $plugins->setFactory('action', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Action($sm->get('url'));
            return $helper;
        });
        $plugins->setFactory('resource', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Resource($sm->get('url'), $serviceLocator->get('cms'));
            $helper->setParser(new PathParser());
            return $helper;
        });
        $plugins->setFactory('document', function($sm) use($serviceLocator) {
                $helper = new ViewHelper\Document($sm->get('url'), $serviceLocator->get('cms'));
                return $helper;
        });
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'Vivo\Repository\Repository' => 'repository',
            ),
            'factories' => array(
                'io_util'            => function(ServiceManager $sm) {
                    $ioUtil     = new \Vivo\IO\IOUtil();
                    return $ioUtil;
                },
                'uuid_generator'    => function(ServiceManager $sm) {
                    $uuidGenerator  = new \Vivo\Uuid\Generator();
                    return $uuidGenerator;
                },
                'uuid_convertor'    => function(ServiceManager $sm) {
                    $indexer        = $sm->get('indexer');
                    $uuidConvertor  =  new \Vivo\Repository\UuidConvertor\UuidConvertor($indexer);
                    return $uuidConvertor;
                },
                'watcher'           => function(ServiceManager $sm) {
                    $watcher        = new \Vivo\Repository\Watcher();
                    return $watcher;
                },
                'storage_util'      => function(ServiceManager $sm) {
                    $ioUtil         = $sm->get('io_util');
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
                    $application            = $sm->get('application');
                    /* @var $application \Zend\Mvc\Application */
                    $appEvents              = $application->getEventManager();
                    $moduleManagerFactory   = new ModuleManagerFactory($modulePaths, $moduleStreamName, $appEvents);
                    return $moduleManagerFactory;
                },
                'Vivo\View\Strategy\PhtmlRenderingStrategy' => function(ServiceManager $sm) {
                    $config = $sm->get('config');
                    $parser = new \Vivo\Util\Path\PathParser();
                    $resolver = new \Vivo\View\Resolver\TemplateResolver($sm->get('module_resource_manager'), $parser, $config['vivo']['templates']);
                    $renderer = new \Vivo\View\Renderer\PhtmlRenderer();
                    $renderer->setResolver($resolver);
                    $renderer->setHelperPluginManager($sm->get('ViewHelperManager'));
                    $strategy = new PhtmlRenderingStrategy($renderer, $resolver);
                    return $strategy;
                },
                'Vivo\CMS\ComponentFactory' => function(ServiceManager $sm) {
                    $di = $sm->get('vivo_service_manager')->get('di_proxy');
                    $cf = new ComponentFactory($di, $sm->get('cms'), $sm->get('site_event')->getSite());
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
                'run_site_manager_listener'  => function(ServiceManager $sm) {
                    $siteManager            = $sm->get('site_manager');
                    $runSiteManagerListener = new \Vivo\SiteManager\Listener\RunSiteManagerListener($siteManager);
                    return $runSiteManagerListener;
                },
                'lucene' => function(ServiceManager $sm) {
                    $storageConfig  = array(
                        'class'     => 'Vivo\Storage\LocalFileSystemStorage',
                        'options'   => array(
                            'root'          => __DIR__ . '/../../data/lucene',
                            'path_builder'  => $sm->get('path_builder'),
                        ),
                    );
                    $storageFactory = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage    = $storageFactory->create($storageConfig);
                    $luceneDirPath  = '/';
                    $luceneDir  = new \Vivo\ZendSearch\Lucene\Storage\Directory\VivoStorage($storage, $luceneDirPath);
                    try {
                        $index      = \ZendSearch\Lucene\Lucene::open($luceneDir);
                    } catch (\ZendSearch\Lucene\Exception\RuntimeException $e) {
                        if ($e->getMessage() == 'Index doesn\'t exists in the specified directory.') {
                            //Index not created yet, create it
                            $index      = \ZendSearch\Lucene\Lucene::create($luceneDir);
                        } else {
                            throw $e;
                        }
                    }
                    return $index;
                },
                'indexer_adapter_lucene'    => function(ServiceManager $sm) {
                    $index                  = $sm->get('lucene');
                    $adapter                = new \Vivo\Indexer\Adapter\Lucene($index);
                    return $adapter;
                },
                'indexer'                   => function(ServiceManager $sm) {
//                    $adapter                = $sm->get('indexer_adapter_lucene');
//                    $indexer                = new \Vivo\Indexer\Indexer($adapter);
                    $indexer                = new \Vivo\Indexer\Dummy();
                    return $indexer;
                },
                'indexer_helper'            => function(ServiceManager $sm) {
                    $indexerHelper          = new \Vivo\Repository\IndexerHelper();
                    return $indexerHelper;
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
                    $indexerHelper          = $sm->get('indexer_helper');
                    $serializer             = new \Vivo\Serializer\Adapter\Entity();
                    $uuidConvertor          = $sm->get('uuid_convertor');
                    $watcher                = $sm->get('watcher');
                    $uuidGenerator          = $sm->get('uuid_generator');
                    $ioUtil                 = $sm->get('io_util');
                    //TODO - supply a real cache
                    $repository             = new \Vivo\Repository\Repository($storage,
                                                                              null,
                                                                              $indexer,
                                                                              $indexerHelper,
                                                                              $serializer,
                                                                              $uuidConvertor,
                                                                              $watcher,
                                                                              $uuidGenerator,
                                                                              $ioUtil);
                    return $repository;
                },
                'cms'                       => function(ServiceManager $sm) {
                    $repository             = $sm->get('repository');
                    $cms                    = new \Vivo\CMS\Api\CMS($repository);
                    return $cms;
                },
                'module_resource_manager'   => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $resourceManagerOptions = $config['vivo']['modules']['resource_manager'];
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $moduleResourceManager  = new \Vivo\Module\ResourceManager\ResourceManager($moduleStorageManager,
                                                                                               $resourceManagerOptions);
                    return $moduleResourceManager;
                },
                'module_install_manager'    => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $cms                    = $sm->get('cms');
                    $dbProviderFactory      = $sm->get('db_provider_factory');
                    $options                = $config['vivo']['module_install_manager'];
                    $moduleInstallManager   = new \Vivo\Module\InstallManager\InstallManager($moduleStorageManager,
                        $cms,
                        $dbProviderFactory,
                        $options);
                    return $moduleInstallManager;
                },
                'pdo_abstract_factory'      => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $pdoConfig              = $config['vivo']['db_service']['abstract_factory']['pdo'];
                    $pdoAf  = new \Vivo\Service\AbstractFactory\Pdo($pdoConfig);
                    return $pdoAf;
                },
                'zdb_abstract_factory'      => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $zdbConfig              = $config['vivo']['db_service']['abstract_factory']['zdb'];
                    $zdbAf  = new \Vivo\Service\AbstractFactory\ZendDbAdapter($zdbConfig);
                    return $zdbAf;
                },
                'cms_api_module'            => function(ServiceManager $sm) {
                    $installManager = $sm->get('module_install_manager');
                    $api            = new \Vivo\CMS\Api\Module($installManager);
                    return $api;
                },
                'db_provider_factory'        => function(ServiceManager $sm) {
                    $dbServiceManager   = $sm->get('db_service_manager');
                    $dbProviderFactory  = new \Vivo\Service\DbProviderFactory($dbServiceManager);
                    return $dbProviderFactory;
                },
                'vivo_service_manager' => function (ServiceManager $sm) {
                    throw new Exception('Vivo service manager is not available until site and modules are loaded.');
                },
                'solr'                      => function(ServiceManager $sm) {
                    $service                = new \Vivo\Indexer\Adapter\Solr\Service('localhost', 8983, '/solr/');
                    return $service;
                },
                'indexer_adapter_solr'      => function(ServiceManager $sm) {
                    $solr               = $sm->get('solr');
                    $adapter            = new \Vivo\Indexer\Adapter\Solr\Adapter($solr);
                    return $adapter;
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
                    $fc->setTreeUtil($sm->get('di')->get('Vivo\UI\TreeUtil'));
                    $fc->setCMS($sm->get('cms'));
                    $fc->setSiteEvent($sm->get('site_event'));
                    return $fc;
                },
                'CLI\Module'    => function(ControllerManager $cm) {
                    $sm                     = $cm->getServiceLocator();
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $remoteModule           = $sm->get('remote_module');
                    $repository             = $sm->get('repository');
                    $moduleApi              = $sm->get('cms_api_module');
                    $controller             = new \Vivo\Controller\CLI\ModuleController($moduleStorageManager,
                                                                                        $remoteModule,
                                                                                        $repository,
                                                                                        $moduleApi);
                    return $controller;
                },
                'ResourceFront'    => function(ControllerManager $cm) {
                    $sm                     = $cm->getServiceLocator();
                    $controller             = new \Vivo\Controller\ResourceFrontController();
                    $controller->setCMS($sm->get('cms'));
                    $controller->setResourceManager($sm->get('module_resource_manager'));
                    $controller->setSiteEvent($sm->get('site_event'));
                    return $controller;
                },
            ),
        );
    }

    public function getConsoleBanner(Console $console)
    {
        return "Vivo 2 CLI\n";
    }

    public function getConsoleUsage(Console $console)
    {
        return array('Available commands:',
                array ('indexer', 'Perform operations on indexer..'),
                array ('info','Show informations about CMS instance.'),
                array ('module', 'Manage modules.'),
        );
    }
}
