<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface, InjectApplicationEventInterface {

	/**
	 * @var Zend\Mvc\MvcEvent
	 */
	protected $event;
	
	public function dispatch(Request $request, Response $response = null) {
		//TODO find document in repository and return it
		$path = $this->event->getRouteMatch()->getParam('path');
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
}
