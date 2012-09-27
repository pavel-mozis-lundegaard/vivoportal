<?php
namespace Vivo;

use Vivo\CMS\Repository;

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
	
	public function getSiteDocument($path, $site = null) {
		//TODO implement
		$document = new \Vivo\CMS\Model\Document();
		return $document;
	}
	
	public function createSiteDocument() {
		
	}
	
	public function removeDocument() {
		
	}
	
	public function addContent() {
		
	}
}
