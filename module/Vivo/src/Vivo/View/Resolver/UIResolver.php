<?php
namespace Vivo\View\Resolver;
use Zend\Mvc\Application;

use Zend\Config\Config;

use Vivo\CMS\Stream\Template;

use Zend\View\Renderer\RendererInterface;

use Zend\View\Resolver\ResolverInterface;

/**
 * Resolver determines wich template file should by used for rendering.
 *
 */
class UIResolver implements ResolverInterface {
	
	/**
	 * @var array
	 */
	private $templateMap = array();
	
	public function __construct($config) {
		$this->configure($config);
		Template::register();
	}
	
	public function configure($config = array()) {
		if (isset($config['templateMap'])) {
			$this->templateMap = array_merge($this->templateMap, $config['templateMap']);
		}		
	}
	
	public function resolve($name, RendererInterface $renderer = null) {
		return $this->getFile($name);
	}
	
	protected function getFile($name) {
		if (isset($this->templateMap[$name])) {
			$file = $this->templateMap[$name];
		} elseif (class_exists($name)) {
			//template isn't in template map, but it is classname
			$file = str_replace('\\', '/', $name).'.phtml';
		}
		return Template::STREAM_NAME.'://'.$file;
	}
}
