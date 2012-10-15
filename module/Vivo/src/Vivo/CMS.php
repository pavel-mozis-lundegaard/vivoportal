<?php
namespace Vivo;

use Vivo\Repository\Repository;

/**
 * Main bussiness class for interact with CMS
 * 
 * @author kormik
 *
 */
class CMS {

	/**
	 * @var Vivo
	 */
	private $repository;
	
	
	/**
	 * Set to use CMS class in the site context.
	 * @var Vivo\CMS\Model\Site
	 */
	private $site; 
	
	public function __construct(Repository $repository) {
		$this->repository = $repository;
	}
	
	public function getSiteByHost($host) {
		//TODO implement
		$site = new \Vivo\CMS\Model\Site();
		return $site;
	}
	
	public function getDocument($path, $site = null) {
		//TODO implement
		//$document = new \Vivo\CMS\Model\Document();
		
		$className = 'Vivo\Repo\\'.str_replace('/', '\\', $path);
		
		$document = new $className();
//		$document = new \Vivo\Mock\Document();
		
		
		
		return $document;
	}
	
	public function getRawContent() {
		return false;
	}
	
	public function createSiteDocument() {
		
	}
	
	public function removeDocument() {
		
	}
	
	public function addContent() {
		
	}
	
	public function getDocumentContents($document) {
		return $document->getContents();
	}
	
	public function getParentDocument($document) {

		$class = get_class($document);
		$pieces = explode('\\', $class);
		array_pop($pieces);
		$class = implode('\\', $pieces);
		
		
		return class_exists($class)? new $class(): false;
		
	}
}
