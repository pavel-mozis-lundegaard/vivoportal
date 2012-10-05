<?php
namespace Vivo\CMS;

use Zend\Di\Di;

use Vivo\CMS;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\CMS\UI\Content\Layout;
use Vivo\UI; 
use Vivo\UI\Component;


/**
 * ComponentFactory is responsible for instatniating UI component for CMS documents. 
 * @author kormik
 *
 */
class ComponentFactory {

	/**
	 * @var \Vivo\CMS
	 */
	private $cms;
	
	/**
	 * @var \Zend\Di\Di
	 */
	private $di;
	
	/**
	 * @param CMS $cms
	 * @param Di $di
	 */
	public function __construct(CMS $cms, Di $di) {
		$this->cms = $cms;
		$this->di = $di;
	}
	
	/**
	 * Returns root UI component for the given document.
	 * @param Document $document
	 * @return \Vivo\UI\Component
	 */
	public function getRootComponent(Document $document) {
		$root = $this->di->get('Vivo\CMS\UI\Root');
		
		if ($content = $this->cms->getRawContent($document)) {
			//component has Raw content...(file, rss, vxf etc.)
			//TODO
			$root->setMain($this->getContentFrontComponent($content, $document));
		} else {
			
			$component = $this->getFrontComponent($document);
			$page = $this->di->get('Vivo\CMS\UI\Page', array('component' => $component));
			$root->setMain($page);
		}
		
		return $root;
	}
	
	/**
	 * Return front component for the given document.
	 * 
	 * @param Document $document
	 * @param array $options (Disable Layout) 
	 * @return \Vivo\UI\Component
	 */
	public function getFrontComponent(Document $document, $options = array()) {
		$contents = $this->cms->getDocumentContents($document);

		if (count($contents) > 1) {
			$frontComponent =  new UI\ComponentContainer();
			$i = 1;
			foreach ($contents as $content) {
				$cc = $this->getContentFrontComponent($content, $document);
				$frontComponent->addComponent($cc, $i++);
			}
		
		} elseif (count($contents) === 1) {
			$frontComponent = $this->getContentFrontComponent(reset($contents), $document);
		} else {
			//TODO throw exception 
		}
		
		if ($layoutPath = $document->getLayout()) {
			$layout = $this->cms->getDocument($layoutPath);
			$frontComponent = $this->applyLayout($layout, $frontComponent);
		}
		
		return $frontComponent;
	}
	
	/**
	 * Wrap the UI component to Layout. 
	 * @param Document $layout
	 * @param Component $component
	 * @return \Vivo\UI\Component
	 */
	public function applyLayout(Document $layout, Component $component) {
		$layoutComponent = $this->getFrontComponent($layout);
		if (!$layoutComponent instanceof Layout) {
			//TODO throw exception
		}
		
		$layoutComponent->setMain($component); 
		
		if ($parentLayout = $this->cms->getParentDocument($layout)) {
			if ($component = $this->applyLayout($parentLayout, $layoutComponent)) {
 				$layoutComponent = $component;
 			}
		}
		return $layoutComponent; 
	}
	
	/**
	 * Instantiates front UI component for the given content.
	 * @param Content $content
	 * @param Document $document
	 * @return \Vivo\UI\Component
	 */
	public function getContentFrontComponent(Content $content, Document $document) {
		//TODO How to find UI component class?
		$className = $content->getFrontComponentClass();
		$component = $this->di->newInstance($className, array('content'=> $content, 'document' => $document));
		return $component;
	}
	
	/**
	 * Instantiates editor UI component for the given content.
	 * @param Content $content
	 * @param Document $document
	 * @return \Vivo\UI\Component
	 */
	public function getEditorComponent(Content $content, Document $document) {
		//TODO implement
	}	
}
