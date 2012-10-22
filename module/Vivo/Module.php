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
use Vivo\Module\ModuleManagerFactory;

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

        //Attach a listener to set up the Site object
        $resolver               = $sm->get('site_resolver');
        $moduleManagerFactory   = $sm->get('module_manager_factory');
        $createSiteListener     = new \Vivo\Site\Listener\CreateSiteListener('host', $resolver, $moduleManagerFactory);
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
                    //TODO - get the site alias -> id map from somewhere or rather configure a proper resolver
                    $map    = array(
                        'www.my-site-alias.com'     => 'www.my-site.com',
                    );
                    $siteResolver   = new \Vivo\Site\Resolver\Map($map);
                    return $siteResolver;
                },
            ),
        );
    }
}
