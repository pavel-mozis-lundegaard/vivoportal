<?php
namespace Vivo\UI;

use Vivo\UI\Exception\ComponentNotExists;

/**
 * @author kormik
 * @todo implement ArrayAccess?
 *
 */

class ComponentContainer extends Component implements ComponentContainerInterface {
	
	/**
	 * @var array of ComponentInterface
	 */
	private $components = array();
	
	public function init() {
		foreach ($this->components as $name => $component) {
			$component->init();
		}
	}
	
	public function done() {
		foreach ($this->components as $name => $component) {
			$component->done();
		}		
	}
	
	public function __get($name) {
		if ($this->hasComponent($name)) {
			return $this->getComponent($name);
		}
		return $this->$name; //notice if property doesn't exist
	}	
	
	function __isset($name) {
		return $this->hasComponent($name);
	}
	
	function __unset($name) {
		if ($this->hasComponent($name))
			$this->removeComponent($name);
	}
	
	public function __set($name, $object) {
		if ($object instanceof ComponentInterface) {
			$this->addComponent($object, $name);
		} else {
			$this->$name = $object;
		}
	}
	
	/* (non-PHPdoc)
	 * @see Vivo\UI.ComponentContainerInterface::addComponent()
	 */
	public function addComponent(ComponentInterface $component, $name = null) {
		//TODO check cycles in component tree
		$component->setParent($this);
		$this->components[$name] = $component;
	}
	
	public function addComponents(){
		//TODO
	}
	
	/* (non-PHPdoc)
	 * @see Vivo\UI.ComponentContainerInterface::removeComponent()
	 */
	public function removeComponent($name) {
		//TODO also accept component object as parameter 
		
		if (!$this->hasComponent($name)) {
			throw new ComponentNotExists(); 
		} 
		$this->getComponent($name)->setParent(null);
		unset($this->components[$name]);
	}
	
	/* (non-PHPdoc)
	 * @see Vivo\UI.ComponentContainerInterface::getComponent()
	 */
	public function getComponent($name) {
		if (!$this->hasComponent($name)) {
			throw new ComponentNotExists();
		}
		return $this->components[$name];
	}
	
	/**
	 * @param string $name
	 */
	public function hasComponent($name) {
		return isset($this->components[$name]);
	}
	
	/**
	 * Retruns UI components tree for debuging.
	 * @return array
	 */
	public function getTree() {
		$tree = array('class' => get_class($this));
		foreach ($this->components as $name => $subComponent) {
			if ($subComponent instanceof self) {
				$tree['sub'][$name] = $subComponent->getTree();
			} else {
				$tree['sub'][$name] = get_class($subComponent);
			}
		}
		return $tree;
	}
}
