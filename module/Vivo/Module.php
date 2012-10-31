<?php
namespace Vivo;

use Vivo\Module\ModuleManagerFactory;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\ControllerManager;

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
                'module_storage'    => function(ServiceManager $sm) {
                    $config         = $sm->get('config');
                    $storageConfig  = $config['vivo']['modules']['storage'];
                    $storageFactory = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage    = $storageFactory->create($storageConfig);
                    return $storage;
                },
                'remote_module'             => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $descriptorName         = $config['vivo']['modules']['descriptor_name'];
                    $storageFactory         = $sm->get('storage_factory');
                    $remoteModule           = new \Vivo\Module\StorageManager\RemoteModule($storageFactory, $descriptorName);
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
                    $manager    = new \Vivo\Module\StorageManager\StorageManager($storage,
                                                                                 $modulePaths,
                                                                                 $descriptorName,
                                                                                 $defaultInstallPath,
                                                                                 $storageUtil,
                                                                                 $remoteModule);
                    return $manager;
                },
                'module_manager_factory'    => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $modulePaths            = $config['vivo']['modules']['module_paths'];
                    $moduleStreamName       = $config['vivo']['modules']['stream_name'];
                    $moduleManagerFactory   = new ModuleManagerFactory($modulePaths, $moduleStreamName);
                    return $moduleManagerFactory;
                },
                'indexer'                   => function(ServiceManager $sm) {
                    $indexer                = new \Vivo\Repository\Indexer\Solr();
                    return $indexer;
                },
                'repository'                => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $storageConfig          = $config['vivo']['cms']['repository']['storage'];
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
                }
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories'     => array(
                'CLI\Module'    => function(ControllerManager $cm) {
                    $sm                     = $cm->getServiceLocator();
                    $moduleStorageManager   = $sm->get('module_storage_manager');
                    $remoteModule           = $sm->get('remote_module');
                    $controller             = new \Vivo\Controller\CLI\ModuleController($moduleStorageManager, $remoteModule);
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
