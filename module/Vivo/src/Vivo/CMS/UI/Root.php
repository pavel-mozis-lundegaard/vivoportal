<?php
namespace Vivo\CMS\UI;

use Vivo\UI\ComponentInterface;
use Vivo\CMS\Model\Document;
use Vivo\UI;
use Zend\Di\Di;
use Zend\Http\Response;

/**
 * @author kormik
 *
 */
class Root extends Component {

	const MAIN_COMPONENT_NAME = 'main';
	const COMPONENT_NAME = 'root';
	
	private $request;

	private $response;

	private $di;

	public function __construct(Response $response) {
		parent::__construct(null, self::COMPONENT_NAME);
		$this->response = $response;
		$response->getHeaders()->addHeaderLine('X-Generated-By: Vivo')
			->addHeaderLine('X-Generated-At: '.gmdate('D, d M Y H:i:s', time()).' GMT');
		
	}
	
	public function setMain(ComponentInterface $component) {
		$this->addComponent($component, self::MAIN_COMPONENT_NAME);
	}

 	public function view() {
 		return $this->getComponent(self::MAIN_COMPONENT_NAME)->view();
 	}
	
	public function getRequest() {
		return $this->request;
	}

	public function setRequest($request) {
		$this->request = $request;
	}

	public function getResponse() {
		return $this->response;
	}

	public function setResponse(Response $response) {
		$this->response = $response;
		$response->setStatusCode(404);
	}
}
