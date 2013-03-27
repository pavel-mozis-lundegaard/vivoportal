<?php
namespace Vivo;

use Vivo\Http\StreamResponseSender;
use Vivo\View\Helper as ViewHelper;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Mvc\SendResponseListener;
use Zend\ServiceManager\ServiceManager;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    /**
     * Module bootstrap method.
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        //Get basic objects
        /** @var $sm ServiceManager */
        $sm             = $e->getApplication()->getServiceManager();
        $eventManager   = $e->getApplication()->getEventManager();
        $config         = $sm->get('config');
        //Initialize logger
        $logger = $sm->get('logger');
        //Initialize translator
        $sm->get('translator');

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //Attach a listener to set up the SiteManager object
        $runSiteManagerListener = $sm->get('run_site_manager_listener');
        $runSiteManagerListener->attach($eventManager);

        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array ($this, 'registerTemplateResolver'));
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array ($this, 'registerViewHelpers'));
        $eventManager->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($logger){
            $logger->info('Matched route: '.$e->getRouteMatch()->getMatchedRouteName());
        });

        $filterListener = $sm->get('Vivo\Http\Filter\OutputFilterListener');
        $filterListener->attach($eventManager);

        //Register response senders
        /** @var $sendResponseListener SendResponseListener */
        $sendResponseListener   = $sm->get('send_response_listener');
        $srlEvents              = $sendResponseListener->getEventManager();
        $streamResponseSender   = new StreamResponseSender();
        $srlEvents->attach(SendResponseEvent::EVENT_SEND_RESPONSE, $streamResponseSender, -2500);
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php'; //main vivo config
        $config['cms'] = include __DIR__ . '/config/cms.config.php'; //CMS config
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Register template resolver.
     * @param MvcEvent $e
     */
    public function registerTemplateResolver(MvcEvent $e)
    {
        $sm = $e->getTarget()->getServiceManager();
        /* @var $viewResolver \Zend\View\Resolver\AggregateResolver */
        $viewResolver = $sm->get('viewresolver');
        $viewResolver->attach($sm->get('template_resolver'), 100);
    }

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
            $match = $serviceLocator->get('application')->getMvcEvent()
                    ->getRouteMatch();
            if ($match instanceof \Zend\Mvc\Router\RouteMatch) {
                $helper->setRouteMatch($match);
            }
            return $helper;
        });

        //set basepath for backend view
        if ($routeName == 'backend/cms/query') {
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
        $resourceRouteName = isset($resourceRouteMap[$routeName])?
        $resourceRouteMap[$routeName]: '';

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

    public function getConsoleBanner(Console $console)
    {
        return "Vivo 2 CLI\n";
    }

    public function getConsoleUsage(Console $console)
    {
        return array('Available commands:',
                array ('indexer', 'Perform operations on indexer..'),
                array ('info','Show information about CMS instance.'),
                array ('module', 'Manage modules.'),
                array ('cms', 'CMS functions.'),
                array ('setup', 'System setup'),
        );
    }
}
