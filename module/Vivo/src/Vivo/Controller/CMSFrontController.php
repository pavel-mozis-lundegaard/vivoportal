<?php
namespace Vivo\Controller;

use Zend\View\Model\ViewModel;

use Zend\Di\Di;

use Vivo\CMS\Stream\Template;

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
		$documentPath = $this->event->getRouteMatch()->getParam('path');
		$di = $this->serviceLocator->get('di');
		$cms = $di->get('cms');

		//TODO get host from routing - this enable selecting site by any part of url (like http://server/www.domain.cz/path...)
		$host = $request->getUri()->getHost();
		$site = $cms->getSiteByHost($host);		
		
		//setup DI with shared instances from Vivo
		$di->instanceManager()->addSharedInstance($request, 'Zend\Http\Request');
		$di->instanceManager()->addSharedInstance($response, 'Zend\Http\Response');
		$di->instanceManager()->addSharedInstance($site, 'Vivo\CMS\Model\Site');
		$di->instanceManager()->addSharedInstance($di, 'Zend\Di\Di');
		
		//\Zend\Di\Display\Console::export($di);
		//die();
		//TODO: add exception when document doesn't exist
		//TODO: redirects based on document properties(https, $document->url etc.)
		
		$document = $cms->getDocument($documentPath, $site);
		$cf = $this->serviceLocator->get('Vivo\CMS\ComponentFactory');
		$root = $cf->getRootComponent($document);
		
		$root->init();
		$result = $root->view();
		
		if ($result instanceof ViewModel) {
			$this->event->setViewModel($result);
		} else {
			//TODO if result is stream,  
		}
	
//		$this->event->setResult($model);
//		return $model;
		
//		$response->setContent($content);
		//return $response;
//		$response->setStatusCode(HttpResponse::STATUS_CODE_200);
//		return $response;
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
