<?php
namespace Vivo\Vmodule;

use Zend\EventManager\EventManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\Listener\ModuleResolverListener;
use Zend\ModuleManager\Listener\AutoloaderListener;
use Zend\ModuleManager\Listener\InitTrigger;
use Zend\ModuleManager\Listener\ConfigListener;

/**
 * VmoduleManagerFactory
 * Factory class for Vmodule manager
 */
class VmoduleManagerFactory
{
    /**
     * Paths to vModules
     * @var array
     */
    protected $vModulePaths     = array();

    /**
     * Constructor
     * @param array $vModulePaths
     */
    public function __construct(array $vModulePaths)
    {
        $this->vModulePaths = $vModulePaths;
    }

    /**
     * Creates and returns a new Vmodule manager instance
     * @param array $vModuleNames
     * @return \Zend\ModuleManager\ModuleManager
     */
    public function getVmoduleManager(array $vModuleNames)
    {
        $events             = new EventManager();
        $moduleAutoloader   = new ModuleAutoloader($this->vModulePaths);
        $configListener     = new ConfigListener();

        // High priority
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($moduleAutoloader, 'register'), 9000);
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE_RESOLVE, new ModuleResolverListener());
        // High priority
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new AutoloaderListener(), 9000);
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new InitTrigger());

        //OnBootstrapListener would be only useful if the Site was refactored to have a bootstrap process,
        //the Vmodule onBootstrap() method could then be called on the Site bootstrap
        //$events->attach(ModuleEvent::EVENT_LOAD_MODULE, new OnBootstrapListener($options));
        //LocatorRegistrationListener is not needed (registers the module with the service manager)
        //$events->attach($locatorRegistrationListener);

        $events->attach($configListener);
        $vModuleManager     = new ModuleManager($vModuleNames, $events);
        $moduleEvent        = new ModuleEvent;
        $vModuleManager->setEvent($moduleEvent);
        return $vModuleManager;
    }
}