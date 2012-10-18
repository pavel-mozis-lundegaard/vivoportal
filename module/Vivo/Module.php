<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Vivo;

use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\ServiceManager;
use Vivo\Vmodule\VmoduleManagerFactory;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //Attach a listener to set up the Site object
        $createSiteListener = new \Vivo\Site\Listener\CreateSiteListener();
        $createSiteListener->attach($eventManager);

        $sm     = $e->getApplication()->getServiceManager();
        /* @var $sm ServiceManager */
        $config = $sm->get('config');

        //Register Vmodule stream
        $vModuleStorage = $sm->get('vmodule_storage');
        $streamName     = $config['vivo']['vmodules']['stream_name'];
        \Vivo\Vmodule\StreamWrapper::register($streamName, $vModuleStorage);
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
                'vmodule_storage'   => function(ServiceManager $sm) {
                    $config         = $sm->get('config');
                    $storageConfig  = $config['vivo']['vmodules']['storage'];
                    $storageFactory = $sm->get('storage_factory');
                    /* @var $storageFactory \Vivo\Storage\Factory */
                    $storage    = $storageFactory->create($storageConfig);
                    return $storage;
                },
                'vmodule_manager_factory'   => function(ServiceManager $sm) {
                    $config                 = $sm->get('config');
                    $vModulePaths           = $config['vivo']['vmodules']['vmodule_paths'];
                    $vModuleStreamName      = $config['vivo']['vmodules']['stream_name'];
                    $vModuleManagerFactory  = new VmoduleManagerFactory($vModulePaths, $vModuleStreamName);
                    return $vModuleManagerFactory;
                },
            ),
        );
    }
}
