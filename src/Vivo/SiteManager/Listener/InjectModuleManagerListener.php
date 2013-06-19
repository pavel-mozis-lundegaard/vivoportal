<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;
use Vivo\Module\ResourceManager\ResourceManager as ModuleResourceManager;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * InjectModuleManagerListener
 */
class InjectModuleManagerListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Module resource manager
     * @var ModuleResourceManager
     */
    protected $moduleResourceManager;

    /**
     * Constructor
     * @param \Vivo\Module\ResourceManager\ResourceManager $moduleResourceManager
     */
    public function __construct(ModuleResourceManager $moduleResourceManager)
    {
        $this->moduleResourceManager    = $moduleResourceManager;
    }

    /**
     * Attach to an event manager
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(SiteEventInterface::EVENT_LOAD_MODULES_POST,
                                             array($this, 'onLoadModulesPost'));
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
     * Listen to "load_modules.post" event and inject vivo module manager into the module resource manager
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModulesPost(SiteEventInterface $e)
    {
        $moduleManager  = $e->getModuleManager();
        if ($moduleManager) {
            $this->moduleResourceManager->setModuleManager($moduleManager);
//            $e->stopPropagation(true);
            //Performance log
            $events = new \Zend\EventManager\EventManager();
            $events->trigger('log', $this,
                array ('message'    => 'Module manager injected into Module resource manager',
                    'priority'   => \VpLogger\Log\Logger::PERF_FINER));
        }
    }
}