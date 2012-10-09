<?php
namespace Vivo\CMS\UI;

use Vivo\UI;
use Vivo\UI\ComponentInterface;
use Vivo\CMS\Model\Document;

use Zend\Di\Di;
use Zend\Http\Response;

/**
 * @author kormik
 *
 */
class Root extends Component {

	const MAIN_COMPONENT_NAME = 'main';
	const COMPONENT_NAME = 'root';
	
	/**
	 * @var \Zend\Http\Request
	 */
	private $request;

	/**
	 * @var \Zend\Htpp\Response
	 */
	private $response;

	/**
	 * @var \Zend\Di\Di
	 */
	private $di;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response) {
		parent::__construct(null, self::COMPONENT_NAME);
		$this->response = $response;
		$response->getHeaders()->addHeaderLine('X-Generated-By: Vivo')
			->addHeaderLine('X-Generated-At: '.gmdate('D, d M Y H:i:s', time()).' GMT');
	}
	
	/**
	 * Sets main UI component
	 * @param ComponentInterface $component
	 */
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
}
