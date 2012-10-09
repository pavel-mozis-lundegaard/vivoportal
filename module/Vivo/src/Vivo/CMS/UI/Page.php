<?php
namespace Vivo\CMS\UI;

use Vivo\UI\ComponentInterface;

use Zend\Http\Response;

/**
 * @author kormik
 * @todo use Zend\View\Helper\Doctype;
 *
 */
class Page  extends Component {
	
	const MAIN_COMPONENT_NAME = 'main';
	
	const DOCTYPE_XHTML_TRANSITIONAL = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	const DOCTYPE_XHTML_STRICT = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	const DOCTYPE_XHTML = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	const DOCTYPE_XHTML_MOBILE = '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';
	const DOCTYPE_HTML5 = '<!DOCTYPE html>';
	
	/**
	 * @var string
	 */
	static $doctype = self::DOCTYPE_XHTML_TRANSITIONAL;
	
	/**
	 * @var string Page title.
	 */
	public $title;
	/**
	 * @var array
	 */
	public $html_attributes = array();
	/**
	 * @var array HTML metas.
	 */
	public $metas = array();
	/**
	 * @var array HTML Links.
	 */
	public $links = array();
	/**
	 * @var array HTML Links with conditions.
	 * @example
	 * <!--[if IE 6]><link rel="stylesheet" href="/Styles/ie6.css" type="text/css" media="screen, projection"/><![endif]-->
	 */
	public $conditional_links = array();
	/**
	 * @var array
	 */
	public $scripts = array(
			array(
					'type'		=> 'text/javascript',
					'src'		=> '/Resources/Scripts/vivo.js'
			)
	);
	
	/**
	 * @param ComponentInterface $component
	 * @param array $options
	 */
	public function __construct(Response $response, $options = null) {
		parent::__construct(null, null);
		$response->getHeaders()->addHeaderLine('Content-Type: text/html');
		if (isset($options['doctype'])) {
			$this->setDoctype($options['doctype']);
		}
	}
	
	/**
	 * Sets main UI component of the page. 
	 * @param ComponentInterface $component
	 */
	public function setMain(ComponentInterface $component) {
		$this->addComponent($component, self::MAIN_COMPONENT_NAME);
	}
	
	/**
	 * Sets HTML doctype of page.
	 * @param unknown_type $doctype
	 */
	public function setDoctype($doctype) {
		$this->doctype = $doctype;
	}
	
	//TODO methods for modifying html head (css, js, keywords etc.)
	//TODO implement resource(js,css) merging
	
}
