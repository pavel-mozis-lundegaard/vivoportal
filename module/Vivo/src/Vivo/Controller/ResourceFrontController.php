<?php
namespace Vivo\Controller;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\EventManager\EventInterface as Event;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;


/**
 * Controller for giving all resource files
 * @author kormik
 */
class ResourceFrontController implements DispatchableInterface, InjectApplicationEventInterface {

	protected $event;

	public function dispatch(Request $request, Response $response = null) {
		//TODO find resource file by path and return it
		$path = $this->event->getRouteMatch()->getParam('path');
		$module = $this->event->getRouteMatch()->getParam('module');
		$response->setContent("Resource file in module: '$module' path: '$path' ");
		$response->setStatusCode(HttpResponse::STATUS_CODE_200);
		return $response;
	}		
	
	public function setEvent(Event $event) {
		$this->event = $event;
	}
	
	public function getEvent() {
		return $this->event;
	}
}
