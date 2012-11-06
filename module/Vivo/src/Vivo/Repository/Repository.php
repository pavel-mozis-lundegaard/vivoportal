<?php
namespace Vivo\Repository;

use Vivo;
use Vivo\CMS;
use Vivo\CMS\Model;
use Vivo\Storage;
use Vivo\Indexer\Indexer;
use Vivo\Repository\UuidConvertor\UuidConvertorInterface;
use Vivo\Repository\Watcher;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\Uuid\GeneratorInterface as UuidGenerator;
use Vivo\IO\IOUtil;
use Vivo\Repository\UnitOfWork\UnitOfWorkInterface;

use Zend\Cache\Storage\StorageInterface as Cache;

/**
 * Repository class provides methods to work with CMS repository.
 * Repository supports transactions. The saveEntity or deleteEntity statement begins a new transaction.
 * Commit method commits the current transaction, making its changes permanent.
 * Rollback rolls back the current transaction, canceling its changes.
 */
class Repository implements RepositoryInterface
{

	const ENTITY_FILENAME = 'Entity.object';

	const UUID_PATTERN = '[\d\w]{32}';

//	const URL_PATTERN = '\/[\w\d\-\/\.]+\/';

	/**
	 * @var \Vivo\Storage\StorageInterface
	 */
	private $storage;

	/**
	 * @var \Vivo\Indexer\Indexer
	 */
	private $indexer;

    /**
     * UUID Convertor
     * @var UuidConvertorInterface
     */
    protected $uuidConvertor;

    /**
     * Object watcher
     * @var Watcher
     */
    protected $watcher;

	/**
	 * @var \Zend\Serializer\Adapter\AdapterInterface
	 */
	private $serializer;

    /**
     * The cache object
     * @var Cache
     */
    protected $cache;

    /**
     * PathBuilder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * UUID Generator
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    /**
     * @var IOUtil
     */
    protected $ioUtil;

    /**
     * Unit of work
     * @var UnitOfWorkInterface
     */
    protected $unitOfWork;

    /**
     * Constructor
     * @param \Vivo\Storage\StorageInterface $storage
     * @param \Zend\Cache\Storage\StorageInterface $cache
     * @param \Vivo\Indexer\Indexer $indexer
     * @param \Zend\Serializer\Adapter\AdapterInterface $serializer
     * @param UuidConvertor\UuidConvertorInterface $uuidConvertor
     * @param Watcher $watcher
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param \Vivo\IO\IOUtil $ioUtil
     * @param UnitOfWork\UnitOfWorkInterface $unitOfWork
     */
    public function __construct(Storage\StorageInterface $storage, Cache $cache = null, Indexer $indexer,
                                \Zend\Serializer\Adapter\AdapterInterface $serializer,
                                UuidConvertorInterface $uuidConvertor,
                                Watcher $watcher,
                                PathBuilderInterface $pathBuilder,
                                UuidGenerator $uuidGenerator,
                                IOUtil $ioUtil,
                                UnitOfWorkInterface $unitOfWork)
	{
		$this->storage          = $storage;
        $this->cache            = $cache;
		$this->indexer          = $indexer;
		$this->serializer       = $serializer;
        $this->uuidConvertor    = $uuidConvertor;
        $this->watcher          = $watcher;
        $this->pathBuilder      = $pathBuilder;
        $this->uuidGenerator    = $uuidGenerator;
        $this->ioUtil           = $ioUtil;
        $this->unitOfWork       = $unitOfWork;
	}

	/**
	 * Returns entity from CMS repository by its identification.
	 * @param string $ident entity identification (path, UUID or symbolic reference)
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
     * @todo OBSOLETE
	 */
	public function getEntity_Old($ident, $throwException = true)
	{
		$uuid = null;
		if (preg_match('/^\[ref:('.self::UUID_PATTERN.')\]$/i', $ident, $matches)) {
			// symbolic reference in [ref:uuid] format
			$uuid = strtoupper($matches[1]);
		} elseif (preg_match('/^'.self::UUID_PATTERN.'$/i', $ident)) {
			// UUID
			$uuid = strtoupper($ident);
		}
		if ($uuid) {
			throw new \Exception('TODO');

			/* UUID */
			$query = new \Vivo\Indexer\Query('SELECT Vivo\CMS\Model\Entity\uuid = :uuid');
			$query->setParameter('uuid', $uuid);

			$entities = $this->indexer->execute($query);

			if (!empty($entities)) {
				return $entities[0];
			}
			if ($throwException)
				throw new Exception\EntityNotFoundException(sprintf('Entity not found; UUID: %s', $uuid));
			return null;
		} else {
			/* path */
			//$ident = str_replace('//', '/', $ident); //s prechodem na novy zapis cest se muze vyskytnout umisteni 2 lomitek
// 			$path = $ident.((substr($ident, -1) == '/') ? '' : '/').self::ENTITY_FILENAME;
// 			$cache_mtime = CMS::$cache->mtime($path);
// 			if (($cache_mtime == Util\FS\Cache::NOW) || (($storage_mtime = $this->storage->mtime($path)) && ($cache_mtime > $storage_mtime))) {
// 				// cache access
// 				$entity = CMS::$cache->get($path, false, true);
// 			} else {
// 				// repository access
// 				CMS::$cache->remove($path); // first remove entity from 2nd level cache
// 				if (!$this->storage->contains($path)) {
// 					if ($throwException)
// 						throw new CMS\EntityNotFoundException(substr($path, 0, -strlen(self::ENTITY_FILENAME)));
// 					return null;
// 				}

// 				try {
// 					$entity = Util\Object::unserialize($this->storage->get($path));
// 				} catch (\Exception $e) {
// 					if ($throwException)
// 						throw $e;
// 					return null;
// 				}
// 				CMS::$cache->set($path, $entity, true);
// 			}

// 			$entity = CMS::convertReferencesToURLs($entity);
// 			$entity->setPath(substr($path, 0, strrpos($path, '/'))); // set volatile path property of entity instance

			$path = $ident . '/' . self::ENTITY_FILENAME;
			//@todo: tohle uz obsahuje metoda get
			if($throwException && !$this->storage->contains($path)) {
				throw new \Exception(sprintf('Entity not found; ident: %s, path: %s', $ident, $path));
			}

			$entity = $this->serializer->unserialize($this->storage->get($path));
			$entity->setPath($ident); // set volatile path property of entity instance

			return $entity;
		}
	}

    /**
     * Returns entity from repository
     * If the entity does not exist, returns null
     * @param string $ident Entity identification (path, UUID or symbolic reference)
     * @return null|\Vivo\CMS\Model\Entity
     * @throws Exception\EntityNotFoundException
     */
    public function getEntity($ident)
    {
        $uuid   = null;
        $path   = null;
        if (preg_match('/^'.self::UUID_PATTERN.'$/i', $ident)) {
            //UUID
            $uuid = strtoupper($ident);
        } elseif (preg_match('/^\[ref:('.self::UUID_PATTERN.')\]$/i', $ident, $matches)) {
            //Symbolic reference in [ref:uuid] format
            $uuid = strtoupper($matches[1]);
        } else {
            //Attempt conversion from path
            $uuid = $this->uuidConvertor->getUuidFromPath($ident);
            if ($uuid) {
                $path   = $ident;
            }
        }
        if (!$uuid) {
            throw new Exception\EntityNotFoundException(
                sprintf("%s: Cannot get UUID for entity identifier '%s'", __METHOD__, $ident));
        }
        //Get entity from watcher
        $entity = $this->watcher->get($uuid);
        if ($entity) {
            return $entity;
        }
        //Get entity from cache
        if ($this->cache) {
            $cacheSuccess   = null;
            $entity         = $this->cache->getItem($uuid, $cacheSuccess);
            if ($cacheSuccess) {
                $this->watcher->add($entity);
                return $entity;
            }
        }
        //Get entity from storage
        if (!$path) {
            $path   = $this->uuidConvertor->getPathFromUuid($uuid);
            if (!$path) {
                throw new Exception\EntityNotFoundException(
                    sprintf("%s: Cannot get path for UUID = '%s'", __METHOD__, $uuid));
            }
        }
        $pathComponents = array($path, self::ENTITY_FILENAME);
        $fullPath       = $this->pathBuilder->buildStoragePath($pathComponents, true);
        if ($this->storage->isObject($fullPath)) {
            $entitySer      = $this->storage->get($fullPath);
            $entity         = $this->serializer->unserialize($entitySer);
            /* @var $entity \Vivo\CMS\Model\Entity */
            //TODO - why setPath()?
            $entity->setPath($ident); // set volatile path property of entity instance
            $this->watcher->add($entity);
            if ($this->cache) {
                $this->cache->addItem($uuid, $entity);
            }
        } else {
            $entity = null;
        }
        return $entity;
    }

	/**
     * Returns parent folder
     * If there is no parent folder (ie this is a root), returns null
	 * @param \Vivo\CMS\Model\Folder $folder
	 * @return \Vivo\CMS\Model\Folder
	 */
	public function getParent(Model\Folder $folder)
	{
        $pathElements       = $this->pathBuilder->getStoragePathComponents($folder->getPath());
        if (count($pathElements) == 0) {
            //$folder is a root folder
            return null;
        }
        array_pop($pathElements);
        $parentFolderPath   = $this->pathBuilder->buildStoragePath($pathElements, true);
        $parentFolder       = $this->getEntity($parentFolderPath);
		return $parentFolder;
	}

    /**
     * Return subdocuments
     * When $deep == true, returns descendants rather than children
     * @param \Vivo\CMS\Model\Entity $entity
     * @param bool|string $className
     * @param bool $deep
     * @return array
     */
    public function getChildren(Model\Entity $entity, $className = false, $deep = false)
	{
		$children       = array();
		$descendants    = array();
		$path           = $entity->getPath();
		//TODO: tady se zamyslet, zda neskenovat podle tridy i obsahy
		//if (is_subclass_of($class_name, 'Vivo\Cms\Model\Content')) {
		//	$names = $this->storage->scan("$path/Contents");
		//}
		//else {
			$names = $this->storage->scan($path);
		//}
		sort($names); // sort it in a natural way

		foreach ($names as $name) {
            $childPath  = $this->pathBuilder->buildStoragePath(array($path, $name), true);
			if (!$this->storage->isObject($childPath)) {
				$entity = $this->getEntity($childPath);
				if ($entity/* && ($entity instanceof CMS\Model\Site || CMS::$securityManager->authorize($entity, 'Browse', false))*/)
					$children[] = $entity;
			}
		}

		// sorting
// 		$entity = $this->getEntity($path, false);
// 		if ($entity instanceof CMS\Model\Entity) {
			//@todo: sorting? jedine pre Interface "SortableInterface" ?
// 			$sorting = method_exists($entity, 'sorting') ? $entity->sorting() : $entity->sorting;
// 			if (Util\Object::is_a($sorting, 'Closure')) {
// 				usort($children, $sorting);
// 			} elseif (is_string($sorting)) {
// 				$cmp_function = 'cmp_'.str_replace(' ', '_', ($rpos = strrpos($sorting, '_')) ? substr($sorting, $rpos + 1) : $sorting);
// 				if (method_exists($entity, $cmp_function)) {
// 					usort($children, array($entity, $cmp_function));
// 				}
// 			}
// 		}

		//All descendants
		foreach ($children as $child) {
			if(!$className || $child instanceof $className) {
				$descendants[] = $child;
			}
			if ($deep) {
                $childDescendants   = $this->getChildren($child, $className, $deep);
				foreach ($childDescendants as $descendant) {
					$descendants[] = $descendant;
				}
			}
		}

		return $descendants;
	}

	/**
     * Returns true when the folder has children
	 * @param Model\Folder $folder
	 * @return bool
	 */
	public function hasChildren(Model\Folder $folder)
	{
		$path = $folder->getPath();
		foreach ($this->storage->scan($path) as $name) {
            $pathElements   = array($path, $name, self::ENTITY_FILENAME);
            $childPath      = $this->pathBuilder->buildStoragePath($pathElements);
			if ($this->storage->contains($childPath)) {
				return true;
			}
		}
		return false;
	}

    /**
     * Saves entity state to repository.
     * Changes become persistent when commit method is called within request.
     * @param \Vivo\CMS\Model\Entity $entity Entity to save
     * @throws Exception\Exception
     * @return \Vivo\CMS\Model\Entity
     */
	public function saveEntity(Model\Entity $entity)
	{
        $entityPath = $entity->getPath();
		if (!$entityPath) {
			throw new Exception\Exception(
                sprintf("%s: Entity with UUID = '%' has no path set", __METHOD__, $entity->getUuid()));
		}
		//@todo: tohle nemelo by vyhazovat exception, misto zmeny path? - nema tohle resit jina metoda,
        //treba pathValidator; pak asi entitu ani nevracet
        //TODO - possible collisions, PathValidator or revise the path validation in general
		$path   = '';
		$len    = strlen($entityPath);
		for ($i = 0; $i < $len; $i++) {
			$path .= (stripos('abcdefghijklmnopqrstuvwxyz0123456789-_/.', $entityPath{$i}) !== false)
					? $entityPath{$i} : '-';
		}
		$entity->setPath($path);
        $this->unitOfWork->saveEntity($entity);
		return $entity;
	}

	/**
	 * Returns site entity by host name.
	 * @param string $host
	 * @return Vivo\CMS\Model\Site|null
	 */
	function getSiteByHost($host)
	{
		foreach ($this->getChildren(new Model\Folder('')) as $site) {
			if (in_array($host, $site->getHosts())) {
				return $site;
			}
		}
		return null;
	}

	/**
	 * No effect - always transactional.
	 */
	public function begin()
	{
		trigger_error('No effect - always transactional.');
	}

	/**
	 * Commit commits the current transaction, making its changes permanent
	 */
	public function commit()
	{
        $this->unitOfWork->commit();
	}

	protected function reset()
	{
		$this->unitOfWork->reset();
	}

	/**
	 * Rollback rolls back the current transaction, canceling its changes.
	 */
	public function rollback()
	{
        $this->unitOfWork->reset();
	}

	public function writeResource(Model\Entity $entity, $name, \Vivo\IO\InputStreamInterface $stream)
	{
        $pathComponents         = array($entity->getPath(), $name);
		$path                   = $this->pathBuilder->buildStoragePath($pathComponents, true);
		$this->unitOfWork->saveStream($stream, $path);
	}

	public function saveResource(Model\Entity $entity, $name, $data)
	{
        $pathComponents         = array($entity->getPath(), $name);
        $path                   = $this->pathBuilder->buildStoragePath($pathComponents, true);
		$this->unitOfWork->saveData($data, $path);
	}

	/**
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param string $name Resource file name.
	 * @return \Vivo\IO\InputStreamInterface
	 */
	public function readResource(Model\Entity $entity, $name)
	{
        $pathComponents = array($entity->getPath(), $name);
        $path           = $this->pathBuilder->buildStoragePath($pathComponents, true);
        $stream         = $this->storage->read($path);
		return $stream;
	}

	/**
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param string $name
	 * @return string
	 */
	public function getResource(Model\Entity $entity, $name)
	{
        $pathComponents = array($entity->getPath(), $name);
        $path           = $this->pathBuilder->buildStoragePath($pathComponents, true);
        $data           = $this->storage->get($path);
		return $data;
	}

    /**
     * Utility method for Vivo\CMS\Event
     * @param \Vivo\CMS\Model\Document $document
     * @return array
     */
    public function getAllContents(Model\Document $document)
	{
		$return = array();
// 		if($entity instanceof CMS\Model\Document) {
		//@todo:
			$count = $document->getContentCount();
			for ($index = 1; $index <= $count; $index++) {
				$return = array_merge($return, $document->getContents($index));
			}
// 		}
		return $return;
	}

	/**
	 * @param \Vivo\CMS\Model\Entity $entity Entity object.
	 */
	public function deleteEntity(Model\Entity $entity)
	{
		//TODO - check that the entity is empty
        $this->unitOfWork->deleteEntity($entity);
	}

	public function deleteResource(Model\Entity $entity, $name)
	{
        $pathComponents         = array($entity->getPath(), $name);
        $path                   = $this->pathBuilder->buildStoragePath($pathComponents, true);
        $this->unitOfWork->deleteItem($path);
	}

	/**
	 * @todo
	 *
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $target Target path.
	 */
	public function moveEntity(Model\Entity $entity, $target) { }

	/**
	 * @todo
	 *
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $target Target path.
	 */
	public function copyEntity(Model\Entity $entity, $target) { }

	/**
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 */
// 	function move($path, $target) {
// 		if (strpos($target, "$path/") === 0)
// 			throw new CMS\Exception(500, 'recursive_operation', array($path, $target));
// 		$path2 = str_replace(' ', '\\ ', $path);
// 		$this->indexer->deleteByQuery("vivo_cms_model_entity_path:$path2 OR vivo_cms_model_entity_path:$path2/*");
// 		$entity = $this->getEntity($path);
// 		if (method_exists($entity, 'beforeMove')) {
// 			$entity->beforeMove($target);
// 			CMS::$logger->warn('Method '.get_class($entity).'::geforeMove() is deprecated. Use Vivo\CMS\Event methods instead.');
// 		}
// 		$this->callEventOn($entity, CMS\Event::ENTITY_BEFORE_MOVE);
// 		$this->storage->move($path, $target);
// 		$targetEntity = $this->getEntity($target);
// 		$targetEntity->path = rtrim($target, '/'); // @fixme: tady by melo dojit nejspis ke smazani kese, tak aby nova entita mela novou cestu a ne starou
// 		$this->callEventOn($targetEntity, CMS\Event::ENTITY_AFTER_MOVE);
// 		//CMS::$cache->clear_mem(); //@fixme: Dodefinovat metodu v Cache tridach - nestaci definice v /FS/Cache, ale i do /FS/DB/Cache - definovat ICache
// 		$this->reindex($targetEntity, true);
// 		$this->indexer->commit();
// 		return $targetEntity;
// 	}

	/**
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 * @throws Vivo\CMS\Exception 500, Recursive operation
	 */
// 	function copy($path, $target) {
// 		if (strpos($target, "$path/") === 0)
// 			throw new CMS\Exception(500, 'recursive_operation', array($path, $target));
// 		$entity = $this->getEntity($path);
// 		CMS::$securityManager->authorize($entity, 'Copy');
// 		if (method_exists($entity, 'beforeCopy')) {
// 			$entity->beforeCopy($target);
// 			CMS::$logger->warn('Method '.get_class($entity).'::geforeCopy() is deprecated. Use Vivo\CMS\Event methods instead.');
// 		}
// 		$this->callEventOn($entity, CMS\Event::ENTITY_BEFORE_COPY);
// 		$this->storage->copy($path, $target);
// 		if ($entity = $this->getEntity($target, false)) {
// 			if ($entity->title)
// 				$entity->title .= ' COPY';
// 			$this->copy_entity($entity);
// 			$this->commit();
// 			$this->reindex($entity);
// 		}
// 		return $entity;
// 	}

	/**
	 * @param Vivo\CMS\Model\Entity $entity
	 */
    /*
	private function copy_entity($entity) {
		$entity->uuid = CMS\Model\Entity::create_uuid();
		$entity->created = $entity->modified = $entity->published = CMS::$current_time;
		$entity->createdBy = $entity->modifiedBy =
			($user = CMS::$securityManager->getUserPrincipal()) ?
				"{$user->domain}\\{$user->username}" :
				Context::$instance->site->domain.'\\'.Security\Manager::USER_ANONYMOUS;
// 		if (method_exists($entity, 'afterCopy')) {
// 			$entity->afterCopy();
// 			CMS::$logger->warn('Method '.get_class($entity).'::afterCopy() is deprecated. Use Vivo\CMS\Event methods instead.');
// 		}
		CMS::$event->invoke(CMS\Event::ENTITY_AFTER_COPY, $entity);
		$this->saveEntity($entity);
		foreach($this->getAllContents($entity) as $content) {
			$this->copy_entity($content);
		}
		foreach ($entity->getChildren() as $child) {
			$this->copy_entity($child);
		}
	}
    */

    /**
     * Reindex all entities (contents and children) saved under entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @param bool $deep
     * @return int
     */
    public function reindex(Model\Entity $entity, $deep = false)
	{
        //TODO - undefined Entity methods
		$count = 1;
		$this->indexer->save($entity);
		if ($entity instanceof Vivo\CMS\Model\Document) {
			for ($index = 1; $index <= $entity->getContentCount(); $index++)
				foreach ($entity->getContents($index) as $content)
					$count += $this->reindex($content, true);
		}
		if ($deep)
			foreach ($entity->getChildren() as $child)
				$count += $this->reindex($child, $deep);
		return $count;
	}
}
