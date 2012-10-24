<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

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
     * Constructor
     * @param array $globalModules List of modules loaded for the global scope (ie all sites)
     */
    public function __construct(array $globalModules)
    {
        $this->modules    = $this->arrayToLower($globalModules);
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
     * @throws \Vivo\SiteManager\Exception\ConfigException
     * @return void
     */
    public function onCollectModules(SiteEventInterface $e)
    {
        $siteConfig = $e->getSiteConfig();
        if (isset($siteConfig['modules'])) {
            $siteModules    = $this->arrayToLower($siteConfig['modules']);
        } else {
            $siteModules    = array();
        }
        //Add modules required by site to the module stack
        $this->addMissingValues($this->modules, $siteModules);
        //Add modules from dependencies
        $this->addMissingDependencies($this->modules);
        $e->setModules($this->modules);
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

    }

    /**
     * Adds missing values from toAdd array to the base array
     * @param array $base
     * @param array $toAdd
     */
    protected function addMissingValues(array &$base, array $toAdd)
    {
        foreach ($toAdd as $value) {
            $value  = strtolower($value);
            if (!in_array($value, $base)) {
                $base[] = $value;
            }
        }
    }

    /**
     * Converts all array values to lower case
     * @param array $ay
     */
    protected function arrayToLower(array &$ay)
    {
        foreach ($ay as $key => $value) {
            $ay[$key]   = strtolower($value);
        }
    }
}