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
                'module_install_manager'    => function(ServiceManager $sm) {
                    $moduleStorage  = $sm->get('module_storage');
                    /* @var $moduleStorage \Vivo\Storage\StorageInterface */
                    $config         = $sm->get('config');
                    $storageFactory = $sm->get('storage_factory');
                    $modulePaths    = $config['vivo']['modules']['module_paths'];
                    $defaultInstallPath = $moduleStorage->getStoragePathSeparator();
                    $ioUtil         = new \Vivo\IO\IOUtil();
                    $storageUtil    = new \Vivo\Storage\StorageUtil($ioUtil);
                    $installMgr     = new \Vivo\Module\InstallManager\InstallManager(
                                            $moduleStorage, $modulePaths, $defaultInstallPath, $storageUtil, $storageFactory);
                    return $installMgr;
                },
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories'     => array(
                'CLI\Module'    => function(ControllerManager $cm) {
                    $sm             = $cm->getServiceLocator();
                    $installManager = $sm->get('module_install_manager');
                    $controller     = new \Vivo\Controller\CLI\ModuleController($installManager);
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
