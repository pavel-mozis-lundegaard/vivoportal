<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface, InjectApplicationEventInterface, ServiceLocatorAwareInterface {

	/**
	 * @var Zend\Mvc\MvcEvent
	 */
	protected $event;
	
	/**
	 * @var Zend\ServiceManager\ServiceManager
	 */
	private $serviceLocator;
	
	/**
	 * @param Request $request
	 * @param Response $response
	 * @return Response
	 */
	public function dispatch(Request $request, Response $response = null) {
		//TODO find document in repository and return it
		$path = $this->event->getRouteMatch()->getParam('path');
		$host = $this->event->getRouteMatch()->getParam('host'); 
		
		$response->setContent('CMS document for path: '. $path);
		$response->setStatusCode(HttpResponse::STATUS_CODE_200);
		return $response;
	}
	
	/**
	 * @param Event $event
	 */
	public function setEvent(Event $event) {
		$this->event = $event;
	}
	
	/**
	 * @return \Zend\Mvc\MvcEvent
	 */
	public function getEvent() {
		return $this->event;
	}
	
	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public function getServiceLocator() {
		return $this->serviceLocator;
	}
}
