<?php
namespace Vivo\UI;

use Vivo\View\Model\UIViewModel;

/**
 * @author kormik
 */

class Component implements ComponentInterface {

	const COMPONENT_SEPARATOR = '->';
	
	/**
	 * Relative template path
	 * @var string
	 */
	private $template;
	
	/**
	 * @var \Vivo\UI\ComponentInterface
	 */
	private $parent;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var ViewModel
	 */
	protected $view;
	

	public function __construct(ComponentContainerInterface $parent = null, $name = null) {
		if ($name) {
			$this->setName($name);
		}
		if ($parent) {
			$parent->addComponent($this, $name);
		}
	}
	
	public function setView(UIViewModel $view) {
		$this->view = $view;
	}
	
	public function init() {
		
	}
	
// 	public function render() {
// 		ob_start();
// 			$this->view();
// 			$output = ob_get_contents();
// 		ob_end_clean();
// 		return $output;
// 	}
	
// 	public function view() {
// 		$this->template = get_called_class().'.phtml';
// 		include  Template::STREAM_NAME.'://'.$this->template;
// 	}

	public function view() {
//		echo "\n".get_class($this) ."::::". get_class($this->view);
		$this->view->setTemplate($this->getTemplate());
		$this->view->setVariable('component', $this);
		return $this->view;
	}
	
	public function done() {
		
	}
	
	function getPath($path = '') {
		$component = $this;
		while ($component) {
			$name = $component->getName();
			$path = $path ? ($name ? $name. self::COMPONENT_SEPARATOR : $name).$path : $name;
			$component = $component->getParent();
		}
		return $path;
	}
	
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	public function getTemplate() {
		return $this->template?:get_class($this);  
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(ComponentContainerInterface $parent, $name = null) {
		$this->parent = $parent;
		if ($name) {
			$this->setName($name);
		}
	}
	
	public function getName() {
		return $this->name;
	}
	
	private function setName($name) {
		//TODO check name format (alfanum)
		$this->name  = $name;
	}
}
