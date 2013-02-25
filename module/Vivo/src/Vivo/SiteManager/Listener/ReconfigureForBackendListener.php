<?php
namespace Vivo\SiteManager\Listener;

use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\SiteManager\Exception;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * ReconfigureForBackendListener
 */
class ReconfigureForBackendListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Service Manager
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm   = $sm;
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
     * Listen to "load_modules.post" event and update cms configuration for backend
     * @param SiteEventInterface $e
     * @return void
     */
    public function onLoadModulesPost(SiteEventInterface $e)
    {
        $cmsConfig      = $this->sm->get('cms_config');
        $backendConfig  = $e->getBackendConfig();
        unset($cmsConfig['ui']);
        $cmsConfig      = ArrayUtils::merge($cmsConfig, $backendConfig);
        $this->sm->setAllowOverride(true);
        $this->sm->setService('cms_config', $cmsConfig);
        $this->sm->setAllowOverride(false);
        //Reconfigure template resolver
        $this->sm->get('template_resolver')->configure($cmsConfig['templates']);
    }
}
