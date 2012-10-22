<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Vivo;

use Vivo\CMS\ComponentFactory;

use Vivo\View\Strategy\UIRenderingStrategy;

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

        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['vivo']['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);
        
        $eventManager->attach('render', array ($this, 'registerUIRenderingStrategy'), 100);
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
    public function registerUIRenderingStrategy($e) 
    {
    	$app          = $e->getTarget();
    	$locator      = $app->getServiceManager();
    	$view         = $locator->get('Zend\View\View');
    	$UIRendererStrategy = $locator->get('Vivo\View\Strategy\UIRenderingStrategy');
    	$view->getEventManager()->attach($UIRendererStrategy, 100);
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
				'Vivo\View\Strategy\UIRenderingStrategy' => function(ServiceManager $sm) {
					$config = $sm->get('config');
					$resolver = new \Vivo\View\Resolver\UIResolver($config['vivo']['templates']);
					$renderer = new \Vivo\View\Renderer\UIRenderer($resolver);
					$strategy = new UIRenderingStrategy($renderer);
					return $strategy;
				},
				'Vivo\CMS\ComponentFactory' => function(ServiceManager $sm) {
					return new ComponentFactory($sm->get('di'), $sm->get('cms'));
				},
            ),
        );

    }
}
