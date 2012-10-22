<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;
use Zend\EventManager\EventManagerInterface;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface, InjectApplicationEventInterface {

	/**
	 * @var \Zend\Mvc\MvcEvent
	 */
	protected $event;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    public function dispatch(Request $request, Response $response = null) {
		//TODO find document in repository and return it
		$path = $this->event->getRouteMatch()->getParam('path');

        //SiteManager testing
        $sm = $this->event->getApplication()->getServiceManager();
        if ($sm->has('vivo_site_manager')) {
            $site   = $sm->get('vivo_site_manager');
            /* @var $site \Vivo\SiteManager\SiteManagerInterface */
            \Zend\Debug\Debug::dump($site->getSiteId(), 'SiteId');
            \Zend\Debug\Debug::dump($site->getSiteAlias(), 'SiteAlias');
            \Zend\Debug\Debug::dump($site->getConfig(), 'SiteManager config');
            \Zend\Debug\Debug::dump($site->getModules(), 'Modules');
            \Zend\Debug\Debug::dump(get_class($site->getModuleManager()), 'Module manager type');
            //Test class autoloading
            $myObj  = new \Vm1\MyObj();
            $myObj2 = new \Vm2\MyObj();
        } else {
            echo 'Site not found in SM.<br>';
        }
        //END - SiteManager testing

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
