<?php
namespace Vivo\CMS;

use Vivo\CMS\Model;
use Vivo\CMS\Workflow;
use Vivo\CMS\Exception;
use Vivo\Repository\Repository;
use Zend\Config;

/**
 * Main business class for interact with CMS.
 */
class CMS
{
	/**
	 * @var Vivo\Repository\Repository
	 */
	private $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @param string $host
	 * @return \Vivo\CMS\Model\Site
	 */
	public function getSiteByHost($host)
	{
		$site = $this->repository->getSiteByHost($host);

		return $site;
	}

	/**
	 * @param string $name
	 * @param string $domain
	 * @param array $hosts
	 * @return Vivo\CMS\Model\Site
	 */
	public function createSite($name, $domain, array $hosts)
	{
		$site = new Model\Site("/$name");
		$site->setDomain($domain);
		$site->setHosts($hosts);

		$config = "[config]\nvalue = \"data\"\n";

		$root = new Model\Document("/$name/ROOT");
		$root->setTitle('Home');
		$root->setWorkflow('Vivo\CMS\Workflow\Basic');

		$this->repository->saveEntity($site);
		$this->repository->saveResource($site, 'config.ini', $config);
		$this->repository->saveEntity($root);
		$this->repository->commit();

		return $site;
	}

	/**
	 * @param \Vivo\CMS\Model\Site $site
	 * @return array
	 */
	public function getSiteConfig(\Vivo\CMS\Model\Site $site)
	{
		try {
			$string = $this->repository->getResource($site, 'config.ini');
		}
		catch(\Vivo\Storage\Exception\IOException $e) {
			return array();
		}

		$reader = new Config\Reader\Ini();
		$config = $reader->fromString($string);

		return $config;
	}

	/**
	 * @param \Vivo\CMS\Model\Document $document
	 * @return \Vivo\CMS\Workflow\AbstractWorkflow
	 */
	public function getWorkflow(\Vivo\CMS\Model\Document $document)
	{
		return Workflow\Factory::get($document->getWorkflow());
	}

	/**
	 * @todo SITE
	 *
	 * @param string $ident
	 * @param unknown_type $site
	 * @return Vivo\CMS\Model\Entity
	 */
	public function getEntity($ident, Model\Site $site = null)
	{
		return $this->repository->getEntity($ident);
	}

	/**
	 * @param \Vivo\CMS\Model\Folder $folder
	 * @return array
	 */
	public function getChildren(\Vivo\CMS\Model\Folder $folder)
	{
		return $this->repository->getChildren($folder);
	}

	/**
	 * @param \Vivo\CMS\Model\Folder $folder
	 * @return \Vivo\CMS\Model\Folder
	 */
	public function getParent(\Vivo\CMS\Model\Folder $folder)
	{
		return $this->repository->getParent($folder);
	}

	/**
	 * @param \Vivo\CMS\Model\Entity $entity
	 */
	protected function saveEntity(\Vivo\CMS\Model\Entity $entity) {
		$this->repository->saveEntity($entity);
		$this->repository->commit();
	}

	public function saveDocument(Model\Document $document/*, $parent = null*/)
	{
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
	public function moveDocument(Model\Document $document, $target)
	{
		$this->repository->moveEntity($document, $target);
		$this->repository->commit();
	}

	public function removeEntity(Model\Entity $entity)
	{
		$this->repository->deleteEntity($entity);
		$this->repository->commit();
	}

	public function removeDocument(Model\Document $document)
	{
		$this->repository->deleteEntity($document);
		$this->repository->commit();
	}

	/**
	 * @param Vivo\CMS\Model\Content $content
	 * @return Vivo\CMS\Model\Document
	 */
	public function getContentDocument(Model\Content $content)
	{
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

	public function addDocumentContent(\Vivo\CMS\Model\Document $document, Model\Content $content, $index = 0)
	{
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
	public function getDocumentContent(Model\Document $document, $version, $index = 0/*, $state {PUBLISHED}*/)
	{
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
	public function getDocumentContents(Model\Document $document, $version, $index = 0/*, $version, $state {PUBLISHED}*/)
	{
		if(!is_integer($version)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($version)));
		}
		if(!is_integer($index)) {
			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 3, __METHOD__, 'integer', gettype($index)));
		}

		$path = $document->getPath().'/Contents.'.$index.'/'.$version;

		return $this->repository->getChildren($path);
	}

	public function publishContent(Model\Content $content)
	{
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

	public function getAllStates(Model\Document $document)
	{

	}

	public function getAvailableStates(Model\Document $document)
	{

	}

	/**
	 * Nasetuje "libovolny" workflow stav obsahu.
	 * @param Model\Content $content
	 * @param unknown_type $state
	 * @throws \InvalidArgumentException
	 */
	public function setState(Model\Content $content, $state)
	{
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
	public function getPublishedContent(Model\Document $document, $index = 0)
	{
		$index = $index ? $index : 0; //@todo: exception na is_int($index);
		$contents = $this->getContents($document, $index);
		foreach ($contents as $content) {
			if($content->getState() == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
				return $content;
			}
		}

		return null;
	}

// 	public function getContents(Model\Document $document, $index)
// 	{
// 		if(!is_integer($index)) {
// 			throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($index)));
// 		}

// 		return $this->repository->getChildren($document->getPath().'/Contents.'.$index);
// 	}
}
