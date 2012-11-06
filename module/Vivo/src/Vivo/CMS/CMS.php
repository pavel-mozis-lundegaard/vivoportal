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
	 * @param string $name Site name.
	 * @param string $domain Security domain.
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
	 * @param string $path Relative document path in site.
	 * @param \Vivo\CMS\Model\Site $site
	 * @return \Vivo\CMS\Model\Document
	 */
	public function getSiteDocument($path, \Vivo\CMS\Model\Site $site)
	{
		return $this->repository->getEntity($site->getPath().'/ROOT/'.$path);
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
	 * @param string $ident
	 * @return \Vivo\CMS\Model\Entity
	 */
	public function getEntity($ident)
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
	protected function saveEntity(\Vivo\CMS\Model\Entity $entity)
	{
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

	private function removeEntity(Model\Entity $entity)
	{
		$this->repository->deleteEntity($entity);
		$this->repository->commit();
	}

	public function removeDocument(Model\Document $document)
	{
		$this->removeEntity($document);
	}

	/**
	 * @param \Vivo\CMS\Model\Content $content
	 * @return \Vivo\CMS\Model\Document
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

		$version = count($this->getDocumentContents($document, $index));
		$contentPath = $path."/Contents.$index/$version";
		$content->setPath($contentPath);
		$content->setState(Workflow\AbstractWorkflow::STATE_NEW);

		$this->repository->saveEntity($content);
		$this->repository->commit();
	}

	/**
	 * @param \Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @param int $version
	 * @throws \Vivo\CMS\Exception\InvalidArgumentException
	 * @return \Vivo\CMS\Model\Content
	 */
	public function getDocumentContent(Model\Document $document, $index, $version/*, $state {PUBLISHED}*/)
	{
		if(!is_integer($version)) {
			throw new Exception\InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 2, __METHOD__, 'integer', gettype($version)));
		}
		if(!is_integer($index)) {
			throw new Exception\InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of %s, %s given', 3, __METHOD__, 'integer', gettype($index)));
		}

		$path = $document->getPath().'/Contents.'.$index.'/'.$version;

		return $this->repository->getEntity($path);
	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param int $index
	 * @throws \Vivo\CMS\Exception\InvalidArgumentException
	 * @return array
	 */
	public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/)
	{
		if(!is_integer($index)) {
			throw new Exception\InvalidArgumentException(sprintf('Argument %d passed to %s must be an type of integer, %s given', 2, __METHOD__, gettype($index)));
		}

		$path = $document->getPath().'/Contents.'.$index;

		return $this->repository->getChildren(new Model\Entity($path));
	}

	/**
	 * @param \Vivo\CMS\Model\Content $content
	 */
	public function publishContent(Model\Content $content)
	{
		$document = $this->getContentDocument($content);
		$oldConent = $this->getPublishedContent($document, $content->getIndex());

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
	 * @param \Vivo\CMS\Model\Content $content
	 * @param string $state
	 * @throws \Vivo\CMS\Exception\InvalidArgumentException
	 */
	public function setState(Model\Content $content, $state)
	{
		$document = $this->getContentDocument($content);
		$workflow = $this->getWorkflow($document);
		$states = $workflow->getAllStates();

		if(!in_array($state, $states)) {
			throw new Exception\InvalidArgumentException('Unknow state value. Available: '.implode(', ', $states));
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
	public function getPublishedContent(Model\Document $document, $index)
	{
		$index = $index ? $index : 0; //@todo: exception na is_int($index);
		$contents = $this->getDocumentContents($document, $index);
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


	/**
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param string $name
	 * @param string $data
	 */
	public function saveResource(Model\Entity $entity, $name, $data)
	{
	    $this->repository->saveResource($entity, $name, $data);
	    $this->repository->commit();
	}
}
