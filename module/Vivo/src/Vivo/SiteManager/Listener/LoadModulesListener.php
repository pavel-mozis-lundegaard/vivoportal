<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\SiteManager;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\Module\ModuleManagerFactory;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
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
     * Constructor
     * @param \Vivo\Module\ModuleManagerFactory $moduleManagerFactory
     */
    public function __construct(ModuleManagerFactory $moduleManagerFactory)
    {
        $this->moduleManagerFactory = $moduleManagerFactory;
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
     * Listen to "load_modules" event, create the module mgr, load modules, store the module mgr into SiteManager, merge config
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModules(SiteEventInterface $e)
    {
        $site   = $e->getTarget();
        /* @var $site SiteManager */
        $moduleNames = $site->getModules();
        //Create module manager
        $moduleManager  = $this->moduleManagerFactory->getModuleManager($moduleNames);
        //Load modules
        $moduleManager->loadModules();
        //Store module manager into the SiteManager object
        $site->setModuleManager($moduleManager);
        //Merge modules config with the site config (site config overrides the modules config)
        $modulesConfig  = $moduleManager->getEvent()->getConfigListener()->getMergedConfig(false);
        $siteConfig     = $site->getConfig();
        if (!$siteConfig) {
            $siteConfig = array();
        }
        $siteConfig = ArrayUtils::merge($modulesConfig, $siteConfig);
        $site->setConfig($siteConfig);
    }
}