<?php
namespace Vivo\Fake;

use Vivo\Repository\Repository;

/**
 * Fake CMS class
 *
 * @author kormik
 *
 */
class CMS extends \Vivo\CMS\CMS{

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
		$site = new \Vivo\CMS\Model\Site();
		return $site;
	}

	public function getDocument($path, $site = null) {
		$className = 'Vivo\Fake\Repo\\'.str_replace('/', '\\', $path);
		$document = new $className();
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
