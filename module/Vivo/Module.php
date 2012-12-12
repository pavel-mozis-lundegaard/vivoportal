<?php
namespace Vivo;

use Vivo\CMS\ComponentFactory;
use Vivo\CMS\ComponentResolver;
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
            'factories' => array(
                'io_util'            => function(ServiceManager $sm) {
                    $ioUtil     = new \Vivo\IO\IOUtil();
                    return $ioUtil;
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
                'vivo_service_manager' => function (ServiceManager $sm) {
                    throw new Exception('Vivo service manager is not available until site and modules are loaded.');
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
