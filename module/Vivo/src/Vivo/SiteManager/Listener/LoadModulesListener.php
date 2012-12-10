<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\Module\ModuleManagerFactory;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * SiteResolveListener
 */
class LoadModulesListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Module manager factory
     * @var ModuleManagerFactory
     */
    protected $moduleManagerFactory;

    /**
     * Application's service manager
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor
     * @param \Vivo\Module\ModuleManagerFactory $moduleManagerFactory
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function __construct(ModuleManagerFactory $moduleManagerFactory, ServiceManager $sm)
    {
        $this->moduleManagerFactory = $moduleManagerFactory;
        $this->serviceManager       = $sm;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_LOAD_MODULES, array($this, 'onLoadModules'));
    }

    /**
     * Detach all our listeners from the event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to "load_modules" event, create the module mgr, load modules, merge config, save module manager
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModules(SiteEventInterface $e)
    {
        $moduleNames = $e->getModules();
        //Create module manager
        $moduleManager  = $this->moduleManagerFactory->getModuleManager($moduleNames);
        $e->setModuleManager($moduleManager);
        //Load modules
        $moduleManager->loadModules();
        //Merge modules config with the site config (site config overrides the modules config)
        $modulesConfig  = $moduleManager->getEvent()->getConfigListener()->getMergedConfig(false);
        $siteConfig     = $e->getSiteConfig();
        if (!$siteConfig) {
            $siteConfig = array();
        }
        //Merge site config into the modules config and use it as site config
        $siteConfig = ArrayUtils::merge($modulesConfig, $siteConfig);
        $e->setSiteConfig($siteConfig);
        //Merge site config into the main config's 'vivo' namespace
        $mainConfig = $this->serviceManager->get('config');
        $vivoConfig = $mainConfig['vivo'];
        $vivoConfig = ArrayUtils::merge($vivoConfig, $siteConfig);
        $mainConfig['vivo'] = $vivoConfig;
        $this->serviceManager->setService('config', $mainConfig);

        //Prepare Vivo service manager




        $e->stopPropagation(true);
    }

    /**
     * Initialize vivo service manager.
     * This method registers factory for vivo_service_manager to the application service manager.
     * The factory is not registered in service manager configuration to avoid instatniate it until
     * site and modules are loaded.
     *
     * @param MvcEvent $e
     */
    protected function initializeVivoServiceManager(MvcEvent $e)
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
     * Creates the Vivo ServiceManager
     */
    protected function createVivoServiceManager()
    {
        //TODO configure using values from [vivo][service_manager] key in config
        //TODO configure by loaded modules
        $sm = new ServiceManager();
        $sm->addPeeringServiceManager($serviceLocator);
        return $sm;
    }


}
