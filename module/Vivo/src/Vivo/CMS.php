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

	/**
	 * @param string $name
	 * @param string $domain
	 * @param array $hosts
	 * @return Vivo\CMS\Model\Site
	 */
	public function createSite($name, $domain, array $hosts) {
		$site = new Model\Site("/$name");
		$site->setDomain($domain);
		$site->setHosts($hosts);

		$root = new Model\Document("/$name/ROOT");
		$root->setTitle('Home');
		$root->setWorkflow('Vivo\CMS\Workflow\Basic');

		$this->repository->saveEntity($site);
		$this->repository->saveEntity($root);
		$this->repository->commit();

		return $site;
	}

	/**
	 * Enter description here ...
	 * @param \Vivo\CMS\Model\Document $document
	 * @return Vivo\CMS\Workflow\AbstractWorkflow
	 */
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

	/**
	 * @param Vivo\CMS\Model\Content $content
	 * @return Vivo\CMS\Model\Document
	 */
	public function getContentDocument(Model\Content $content) {
		$path = $content->getPath();
		$path = substr($path, 0, strrpos($path, '/') - 1);
		$path = substr($path, 0, strrpos($path, '/'));

		$document = $this->repository->getEntity($path);

		if($document instanceof Model\Document) {
			return $document;
		}

		//@todo: nebo exception
		return null;
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
	 * @param int $version
	 * @param int $index
	 * @throws \InvalidArgumentException
	 * @return Vivo\CMS\Model\Content
	 */
	public function getContent(Model\Document $document, $version, $index = 0/*, $state {PUBLISHED}*/) {
		if(!is_integer($version)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($version)));
		}
		if(!is_integer($index)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 3, __METHOD__, 'integer', gettype($index)));
		}

		$path = $document->getPath().'/Contents.'.$index.'/'.$version;

		return $this->repository->getEntity($path);
	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function getContents(Model\Document $document, $version, $index = 0/*, $version, $state {PUBLISHED}*/) {
		if(!is_integer($version)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($version)));
		}
		if(!is_integer($index)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 3, __METHOD__, 'integer', gettype($index)));
		}

		$path = $document->getPath().'/Contents.'.$index.'/'.$version;

		return $this->repository->getChildren($path);
	}

	public function publishContent(Model\Content $content) {
		$document = $this->getContentDocument($content);
		$oldConent = $this->getPublishedContent($document);

		if($oldConent) {
			$oldConent->setState(Workflow\AbstractWorkflow::STATE_ARCHIVED);
			$this->repository->saveEntity($oldConent);
		}

		$content->setState(Workflow\AbstractWorkflow::STATE_PUBLISHED);
		$this->repository->saveEntity($content);
		$this->repository->commit();
	}

	public function getAllStates(Model\Document $document) {

	}

	public function getAvailableStates(Model\Document $document) {

	}

	/**
	 * Nasetuje "libovolny" workflow stav obsahu.
	 * @param Model\Content $content
	 * @param unknown_type $state
	 * @throws \InvalidArgumentException
	 */
	public function setState(Model\Content $content, $state) {
		$document = $this->getContentDocument($content);
		$workflow = $this->getWorkflow($document);
		$states = $workflow->getAllStates();

		if(!in_array($state, $states)) {
			throw new \InvalidArgumentException('Unknow state value. Available: '.implode(', ', $states));
		}

		if(true /* uzivatel ma pravo na change*/) {

		}

		if($state == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
			$this->publishContent($content);
		}
		else {
			$content->setState($state);
			$this->repository->saveEntity($content);
			$this->repository->commit();
		}
	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @return Vivo\CMS\Model\Content
	 */
	public function getPublishedContent(Model\Document $document, $index = null) {
		$index = $index ? $index : 0;
		$contents = $this->getContents($document, $index);
		foreach ($contents as $content) {
			if($content->getState() == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
				return $content;
			}
		}

		return null;
	}

// 	public function getContents(Model\Document $document, $index) {
// 		if(!is_integer($index)) {
// 			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
// 		}

// 		return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
// 	}
}
