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
                'vmodule_manager_factory'   => function (ServiceManager $sm) {
                    //Register Vmodule stream wrapper
                    $storage    = new \Vivo\Storage\LocalFs(realpath(__DIR__ . '/../../vmodule'));
                    \Vivo\Vmodule\VmoduleStreamWrapper::register($storage);
                    $config                 = $sm->get('config');
                    $vModulePaths           = $config['vivo']['vmodule_paths'];
                    $vModuleManagerFactory  = new VmoduleManagerFactory($vModulePaths);
                    return $vModuleManagerFactory;
                },
            ),
        );
    }
}
