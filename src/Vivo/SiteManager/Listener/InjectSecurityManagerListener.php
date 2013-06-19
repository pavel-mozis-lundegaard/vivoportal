<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * InjectSecurityManagerListener
 */
class InjectSecurityManagerListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Service Locator
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator   = $serviceLocator;
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
     * Listen to "load_modules.post" event and inject security manager into CMS API
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModulesPost(SiteEventInterface $e)
    {
        /** @var $cmsApi \Vivo\CMS\Api\CMS */
        $cmsApi             = $this->serviceLocator->get('Vivo\CMS\Api\CMS');
        $securityManager    = $this->serviceLocator->get('security_manager');
        $cmsApi->setSecurityManager($securityManager);
        //Performance log
        $events = new \Zend\EventManager\EventManager();
        $events->trigger('log', $this,
            array ('message'    => 'Security manager injected into CMS API',
                'priority'   => \VpLogger\Log\Logger::PERF_FINER));
    }
}