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

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
    
    public function getServiceConfig() {
		return array(
			'factories' => array (
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
    
    /**
     * Register rendering strategy fo Vivo UI.
     * 
     * @param unknown_type $e
     */
    public function registerUIRenderingStrategy($e) {
    	$app          = $e->getTarget();
    	$locator      = $app->getServiceManager();
    	$view         = $locator->get('Zend\View\View');
    	$UIRendererStrategy = $locator->get('Vivo\View\Strategy\UIRenderingStrategy');
    	$view->getEventManager()->attach($UIRendererStrategy, 100);
    }
}
