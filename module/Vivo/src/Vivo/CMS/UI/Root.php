<?php
namespace Vivo\CMS\UI;

use Vivo\UI\ComponentInterface;

use Vivo\CMS\Model\Document;

use Vivo\UI;
use Vivo\CMS\Repository;

use Zend\Di\Di;
use Zend\Stdlib\RequestInterface;

/**
 * @author kormik
 *
 */
class Root extends Component {

	const MAIN_COMPONENT_NAME = 'main';
	
	private $request;

	private $response;

	private $di;

	/**
	 * @param Repository $repository
	 * @param Di $di
	 * @param string $documentPath
	 * @todo remove dependency on Di
	 */
	public function __construct(Di $di, Document $document) {
		parent::__construct(null, 'root');
		$this->di = $di;
		
		//TODO implement logic choosing appropriate UI component, depends on content type (File, Link, HyperLink etc.)
		//TODO add response headers (content-type)
				
		$cf = $this->di->get('Vivo\CMS\ComponentFactory');
		$component = $cf->getFrontComponent($document);
		$this->setMain($di->get('Vivo\CMS\UI\Page', array ('component'=> $component)));
	}
	
	private function setMain(ComponentInterface $component) {
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

	public function setResponse($response) {
		$this->response = $response;
	}
}
