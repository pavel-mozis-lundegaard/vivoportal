<?php
namespace Vivo\Service\Listener;

use Vivo\View\Helper as ViewHelper;

use Zend\Mvc\MvcEvent;

/**
 * Class RegisterViewHelpersListener
 * @package Vivo\Service\Listener
 */
class RegisterViewHelpersListener
{
    /**
     * Registers view helpers to the view helper manager.
     * @param MvcEvent $e
     */
    public function registerViewHelpers($e) {
        $application    = $e->getTarget();
        $serviceLocator = $application->getServiceManager();
        $routeName      = $e->getRouteMatch()->getMatchedRouteName();
        /* @var $plugins \Zend\View\HelperPluginManager */
        $plugins        = $serviceLocator->get('view_helper_manager');

        //register url view helper
        $plugins->setFactory('url', function ($sm) use($serviceLocator) {
            $helper = new ViewHelper\Url;
            $router = \Zend\Console\Console::isConsole() ? 'HttpRouter' : 'Router';
            $helper->setRouter($serviceLocator->get($router));
            $match = $serviceLocator->get('application')->getMvcEvent()->getRouteMatch();
            if ($match instanceof \Zend\Mvc\Router\RouteMatch) {
                $helper->setRouteMatch($match);
            }
            return $helper;
        });

        //set basepath for backend view
        if ($routeName == 'backend/cms/query') {
            /** @var $url ViewHelper\Url */
            $url = $plugins->get('url');
            $path = $url('backend/cms/query', array('path'=>''), false);
            $basePath = $plugins->get('basepath');
            $basePath->setBasePath($path);
        }

        //define resources routes for Resource view helper
        $resourceRouteMap = array(
            'vivo/cms'          => 'vivo/resource',
            'backend/cms'       => 'backend/resource',
            'backend/modules'   => 'backend/backend_resource',
            'backend/other'     => 'backend/backend_resource',
            'backend/default'   => 'backend/backend_resource',
        );
        $resourceRouteName = isset($resourceRouteMap[$routeName]) ? $resourceRouteMap[$routeName] : '';

        //register resource view helper
        $plugins->setFactory('resource', function($sm) use($serviceLocator, $resourceRouteName) {
            $helper = new ViewHelper\Resource($serviceLocator->get('Vivo\CMS\Api\CMS'));
            $helper->setResourceRouteName($resourceRouteName);
            return $helper;
        });

        //register document view helper
        $plugins->setFactory('document', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Document($serviceLocator->get('Vivo\CMS\Api\CMS'));
            return $helper;
        });
    }
}
