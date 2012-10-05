<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\Listener\ModuleResolverListener;


/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface, InjectApplicationEventInterface {

	/**
	 * @var Zend\Mvc\MvcEvent
	 */
	protected $event;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    public function dispatch(Request $request, Response $response = null) {
		//TODO find document in repository and return it
		$path = $this->event->getRouteMatch()->getParam('path');

        //TODO - Get list of VModule names for the matched Site
        $vModuleNames   = array('Vm1');
        $events         = new EventManager();
        $moduleAutoloader = new ModuleAutoloader(array('c:/WebDev/Lundegaard/v2/vmodule'));

        // High priority
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($moduleAutoloader, 'register'), 9000);
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE_RESOLVE, new ModuleResolverListener());
        /*
        // High priority
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new AutoloaderListener($options), 9000);
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new InitTrigger($options));
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new OnBootstrapListener($options));
        $events->attach($locatorRegistrationListener);
        $events->attach($configListener);
        */

        $vModuleManager = new ModuleManager($vModuleNames, $events);
        $moduleEvent = new ModuleEvent;
        $vModuleManager->setEvent($moduleEvent);
        $vModuleManager->loadModules();


        $response->setContent('CMS document for path: '. $path);
		$response->setStatusCode(HttpResponse::STATUS_CODE_200);
		return $response;
	}
	
	public function setEvent(Event $event) {
		$this->event = $event;
	}
	
	public function getEvent() {
		return $this->event;
	}


    /**
     * Set the event manager instance
     * @param  EventManagerInterface $events
     * @return CMSFrontController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
            'cms_front_controller',
        ));
        $this->events = $events;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Register the default event listeners
     * @return CMSFrontController
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($this, 'onLoadModules'));
    }

}
