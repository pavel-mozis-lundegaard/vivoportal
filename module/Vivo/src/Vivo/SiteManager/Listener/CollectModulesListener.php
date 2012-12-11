<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * CollectModulesListener
 */
class CollectModulesListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners    = array();

    /**
     * List of modules to load
     * @var array
     */
    protected $modules      = array();

    /**
     * Module Storage Manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * Constructor
     * @param array $globalModules
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     */
    public function __construct(array $globalModules, ModuleStorageManager $moduleStorageManager)
    {
        $this->modules              = $globalModules;
        $this->moduleStorageManager = $moduleStorageManager;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_COLLECT_MODULES, array($this, 'onCollectModules'));
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
     * Listen to "collect_modules" event, merge list of global modules with the site modules and add modules from dependencies
     * @param SiteEventInterface $e
     * @return void
     */
    public function onCollectModules(SiteEventInterface $e)
    {
        $siteConfig = $e->getSiteConfig();
        if ($siteConfig && isset($siteConfig['modules']['site_modules'])) {
            //Add modules required by site to the module stack
            $siteModules    = $siteConfig['modules']['site_modules'];
            foreach ($siteModules as $moduleName => $moduleConfig) {
                if ($moduleConfig['enabled']) {
                    $this->addMissingValue($this->modules, $moduleName);
                }
            }
        }
        //Add modules from dependencies
        $this->addMissingDependencies($this->modules);
        $e->setModules($this->modules);
        $e->stopPropagation(true);
    }

    /**
     * Adds missing dependencies to the list of modules
     * @param array $modules
     */
    protected function addMissingDependencies(array &$modules)
    {
        reset($modules);
        while ($module = current($modules)) {
            $dependencies   = $this->getModuleDependencies($module);
            $this->addMissingValues($modules, $dependencies);
            next($modules);
        }
    }

    /**
     * Returns an array of module names - dependencies of $module
     * If there are no dependencies, returns an empty array
     * @param string $module
     * @return array
     */
    protected function getModuleDependencies($module)
    {
        $moduleInfo = $this->moduleStorageManager->getModuleInfo($module);
        if (isset($moduleInfo['descriptor']['require'])) {
            $dependencies   = array_keys($moduleInfo['descriptor']['require']);
        } else {
            $dependencies   = array();
        }
        return $dependencies;
    }

    /**
     * Adds a value to the base array if it is missing there
     * @param array $base
     * @param string $value
     */
    protected function addMissingValue(array &$base, $value)
    {
        if (!in_array($value, $base)) {
            $base[] = $value;
        }
    }

    /**
     * Adds missing values from toAdd array to the base array
     * @param array $base
     * @param array $toAdd
     */
    protected function addMissingValues(array &$base, array $toAdd)
    {
        foreach ($toAdd as $value) {
            $this->addMissingValue($base, $value);
        }
    }
}