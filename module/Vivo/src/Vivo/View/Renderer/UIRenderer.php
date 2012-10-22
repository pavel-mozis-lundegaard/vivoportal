<?php
namespace Vivo\View\Renderer;

use Vivo\View\Resolver\UIResolver;
use Vivo\View\Exception;

use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\Variables;

/**
 * Renderer for rendering UI view models. Templates are included in it's context.
 * Object properties and local variables are double-underscored, to prevent naming collisions with included templates.
 */
class UIRenderer implements Renderer {

	private $__resolver;
	
	public function __construct(ResolverInterface $resolver) {
		$this->setResolver($resolver);
	}
	
	public function getEngine() {
		return $this;
	}

	public function setResolver(ResolverInterface $resolver) {
		$this->__resolver = $resolver;
	}
	
	/* (non-PHPdoc)
	 * @see Zend\View\Renderer.RendererInterface::render()
	 */
	public function render($model, $values = null) {
		if (!$model instanceof ModelInterface)	{
			throw new Exception\InvalidArgumentException('Bad Argument'); 
		}
		if (!$model->getTemplate()) {
			throw new Exception\InvalidArgumentException('Missing model template.');
		}
		
		$values = $model->getVariables();
		
		if (null !== $values) {
			$this->setVars($values);
		}
		unset($values);
		
		// extract all assigned vars (pre-escaped), but not 'this'.
		$__vars = $this->vars()->getArrayCopy();
		if (array_key_exists('this', $__vars)) {
			unset($__vars['this']);
		}
		extract($__vars);
		unset($__vars); // remove $__vars from local scope
		
		
		$this->__file = $this->__resolver->resolve($model->getTemplate()); 
		
		ob_start();
		include $this->__file;
		$this->__content = ob_get_clean();
		
		return $this->__content;
	}
	
	public function setVars($variables) {
		if (!is_array($variables) && !$variables instanceof ArrayAccess && !$variables instanceof Variables) {
			throw new Exception\InvalidArgumentException(sprintf(
					'Expected array, ArrayAccess or Variables object; received "%s"',
					(is_object($variables) ? get_class($variables) : gettype($variables))
			));
		}
	
		// Enforce a Variables container
		if (!$variables instanceof Variables) {
			$variablesAsArray = array();
			foreach ($variables as $key => $value) {
				$variablesAsArray[$key] = $value;
			}
			$variables = new Variables($variablesAsArray);
		}
	
		$this->__vars = $variables;
		return $this;
	}
	
	public function vars($key = null) {
		if (null === $this->__vars) {
			$this->setVars(new Variables());
		}
	
		if (null === $key) {
			return $this->__vars;
		}
		return $this->__vars[$key];
	}
}
