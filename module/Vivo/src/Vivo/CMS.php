<?php
namespace Vivo;

use Vivo\CMS\Model;
use Vivo\CMS\Workflow;
use Vivo\Repository\Repository;

/**
 * Main business class for interact with CMS.
 *
 * @author miroslav.hajek
 */
class CMS {
	/**
	 * @var Vivo\Repository\Repository
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
		$site = new Model\Site();
		return $site;
	}

	public function createSite($name, $domain, array $hosts) {
		$site = new Model\Site("/$name");
		$site->setDomain($domain);
		$site->setHosts($hosts);

		$this->repository->saveEntity($site);
		$this->repository->commit();

		return $site;
	}

	public function getWorkflow(\Vivo\CMS\Model\Document $document) {
		return Workflow\Factory::get($document->getWorkflow());
	}

	/**
	 * @todo SITE
	 *
	 * @param string $ident
	 * @param unknown_type $site
	 * @return Vivo\CMS\Model\Document
	 */
	public function getDocument($ident, $site = null) {
		$document = $this->repository->getEntity($ident);

		if($document instanceof Model\Document) {
			return $document;
		}
	}

	public function createDocument() {

	}

	protected function saveEntity(\Vivo\CMS\Model\Entity $entity) {
		$this->repository->saveEntity($entity);
		$this->repository->commit();
	}

	public function saveDocument(Model\Document $document/*, $parent = null*/) {
/*
		if($parent != null && !$parent instanceof Model\Document && !$parent instanceof Model\Site) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an instance of %s',
				2, __METHOD__, implode(', ', array('Vivo\Model\Document', 'Vivo\Model\Site')))
			);
		}
*/
		$this->repository->saveEntity($document);
		$this->repository->commit();
	}

	/**
	 * @param Model\Document $document
	 * @param string $target Path.
	 */
	public function moveDocument(Model\Document $document, $target) {

	}

	public function removeDocument(Model\Document $document) {
		$this->repository->deleteEntity($document);
		$this->repository->commit();
	}

	public function getContentDocument(Model\Content $content) {
		$path = $content->getPath();
		$path = substr($path, 0, strrpos($path, '/') - 1);
		$path = substr($path, 0, strrpos($path, '/'));

		$document = $this->repository->getEntity($path);

		if($document instanceof Model\Document) {
			return $document;
		}
	}

	public function addContent(\Vivo\CMS\Model\Document $document, Model\Content $content, $index = 0) {
		$path = $document->getPath();

		$version = count($this->getContents($document, $index));
		$contentPath = $path."/Contents.$index/$version";
		$content->setPath($contentPath);

		$this->repository->saveEntity($content);
		$this->repository->saveEntity($document);
		$this->repository->commit();
	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function getContent(Model\Document $document, $index, $version/*, $state {PUBLISHED}*/) {
		if(!is_integer($index)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
		}

		return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function getContents(Model\Document $document, $index/*, $version, $state {PUBLISHED}*/) {
		if(!is_integer($index)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
		}

		return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
	}

	public function publishContent(Model\Content $content) {

	}

	public function getPublishedContent(Model\Document $document, $index = null) {
		$index = $index ? $index : 0;
		$contents = $this->getContents($document, $index);
		foreach ($contents as $content) {
			if($content->getState() == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
				return $content;
			}
		}
	}

// 	public function getContents(Model\Document $document, $index) {
// 		if(!is_integer($index)) {
// 			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
// 		}

// 		return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
// 	}
}
