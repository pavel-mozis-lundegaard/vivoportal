<?php
namespace Vivo;

use Vivo\Util\Path\PathParser;
use Vivo\View\Helper as ViewHelper;
use Vivo\Service\Exception;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
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
     * @param MvcEvent $e
     */
    public function registerUIRenderingStrategies(MvcEvent $e)
    {
        $app          = $e->getTarget();
        $locator      = $app->getServiceManager();
        $view         = $locator->get('Zend\View\View');
        $phtmlRenderingStrategy = $locator->get('phtml_rendering_strategy');
        $view->getEventManager()->attach($phtmlRenderingStrategy, 100);
    }

    /**
     * Registers view helpers to the view helper manager.
     * @param MvcEvent $e
     */
    public function registerViewHelpers($e) {
        $app          = $e->getTarget();
        $serviceLocator      = $app->getServiceManager();
        /** @var $plugins \Zend\View\HelperPluginManager */
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
        $plugins->setFactory('vivoform', function($sm) {
            $helper = new ViewHelper\VivoForm();
            return $helper;
        });
        $plugins->setFactory('vivoformfieldset', function($sm) {
            $helper = new ViewHelper\VivoFormFieldset();
            return $helper;
        });
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'vivo_service_manager' => function (ServiceManager $sm) {
                    //TODO - this exception is caught!
                    throw new Exception\ServiceNotAvailableException(
                        sprintf('%s: Vivo service manager is not available until site and modules are loaded.',
                        __METHOD__));
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
                array ('info','Show information about CMS instance.'),
                array ('module', 'Manage modules.'),
        );
    }
}
