<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Repository.php 2197 2012-08-20 08:21:28Z pkrajcar $
 */
namespace Vivo\Repository;

use Vivo;
// use Vivo\Context;
use Vivo\CMS;
use Vivo\CMS\Model;
use Vivo\CMS\Security;
use Vivo\Util;
//use Vivo\Logger;
use Vivo\Storage;
use Vivo\Repository\Indexer;

/**
 * Repository class provides methods to works with CMS repository.
 * Repository works transactionally. The saveEntity or deleteEntity statement begins a new transaction. Commit method commits the current transaction, making its changes permanent.
 * Rollback rolls back the current transaction, canceling its changes.
 * @author tzajicek
 * @version 1.0
 */
class Repository implements RepositoryInterface {

	const ENTITY_FILENAME = 'Entity.object';

	const UUID_PATTERN = '[\d\w]{32}';

//	const URL_PATTERN = '\/[\w\d\-\/\.]+\/';

	/**
	 * @var \Vivo\Repository\Storage\StorageInterface
	 */
	private $storage;
	/**
	 * @var \Vivo\Repository\Indexer\IndexerInterface
	 */
	private $indexer;
	/**
	 * @var \Zend\Serializer\Adapter\AdapterInterface
	 */
	private $serializer;
	/**
	 * @var array The list of entities that are prepared to impose.
	 */
	private $saveEntities = array();
	/**
	 * @var array The list of (reource) files that are prepared to impose.
	 */
	private $saveFiles = array();
	/**
	 * @var array The list of files that are prepared to copy.
	 */
	private $copyFiles = array();
	/**
	 * @var array The list of resource files and entities that are prepared to delete.
	 */
	private $deletePaths = array();
	/**
	 * @var array The list of entities that are prepared to delete.
	 */
	private $deleteEntities = array();

	/**
	 * @param Vivo\Repository\Storage\StorageInterface $storage
	 * @param Vivo\Repository\Indexer\IndexerInterface $indexer
	 */
	public function __construct(Storage\StorageInterface $storage, $cache, Indexer\IndexerInterface $indexer, \Zend\Serializer\Adapter\AdapterInterface $serializer) {
		$this->storage = $storage;
		$this->indexer = $indexer;
		$this->serializer = $serializer;
	}

	/**
	 * Creates new UUID. UUID is 32-character unique hexadecimal number.
	 * @example A6E7FC1218C8725AFC9A6B6A3D003435
	 * @return string
	 *
	 * @todo: nejaky generator? predhazovat pres DI objekt ktery tohle zajisti, treba i unikatnost
	 */
	public static function createUuid() {
		return strtoupper(md5(uniqid()));
	}

	/**
	 * Returns entity from CMS repository by its identification.
	 * @param string $ident entity identification (path, UUID or symbolic reference)
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
	 */
	public function getEntity($ident, $throwException = true) {
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
			$query = new Indexer\Query('SELECT Vivo\CMS\Model\Entity\uuid = :uuid');
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

// 			$this->storage->

			//@todo: tohle uz obsahuje metoda get
			if($throwException && !$this->storage->contains($ident)) {
				throw new \Exception('Entity not found: '.$ident);
			}

			$entity = $this->serializer->unserialize($this->storage->get($ident));
// print_r($entity);

			return $entity;
		}
	}

	/**
	 * Convert entity UUID to repository path.
	 * @author mhajek
	 * @param string $uuid		Entity UUID.
	 * @return string|null		Entity path.
	 */
	public function getEntityPathByUuid($uuid) {
		$paths = array();
		$query = new Indexer\Query('SELECT Vivo\CMS\Model\Entity\uuid = :uuid');
		$query->setParameter('uuid', $uuid);
		$query->setMaxResults(1);

		$facetPaths = $this->indexer->execute($query//,
										/*array(
											'limit' => 0,
											'facet' => 'on',
											'facet.field' => array('vivo_cms_model_entity_path'),
											'facet.limit' => 2 // Nemuze byt maximum, bohuzel existuje mnoho duplicitnich UUID na starych webech.
										),*/
										/*Indexer::H*/);

		foreach ($facetPaths as $path => $count) {
			if($count) $paths[] = $path;
		}

// 		if (count($paths) > 1 && (CMS::$logger->level >= Logger::LEVEL_WARN)) {
// 			CMS::$logger->warn("Multiple entities with the same UUID $uuid detected (".implode(', ', $paths).')');
// 		}

		return isset($paths[0]) ? $paths[0] : null;
	}

	/**
	 * Return subdocuments.
	 * @param string $path Entity path in CMS repository.
	 * @param string $class_name Class name for filtering.
	 * @param int $deep
	 * @return array
	 */
	public function getChildren(Model\Entity $entity, $class_name = false, $deep = false, $throw_exception = true) {
		$children = array();
		$descendants = array();

		$path = $entity->getPath();
		//TODO: tady se zamyslet, zda neskenovat podle tridy i obsahy
		//if (is_subclass_of($class_name, 'Vivo\Cms\Model\Content')) {
		//	$names = $this->storage->scan("$path/Contents");
		//}
		//else {
			$names = $this->storage->scan($path);
		//}
		sort($names); // sort it in a natural way

		foreach ($names as $name) {
			if ($name{0} != '.') {
				$child_path = "$path/$name";
				if ($this->storage->contains($child_path)) {
					//echo "$child_path<br>";
					$entity = $this->getEntity($child_path, $throw_exception);
					if ($entity/* && ($entity instanceof CMS\Model\Site || CMS::$securityManager->authorize($entity, 'Browse', false))*/)
						$children[] = $entity;
				}
			}
		}
		// sorting
		$entity = $this->getEntity($path, false);
		if ($entity instanceof CMS\Model\Entity) {
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
		}

		//all descendants
		foreach ($children as $child) {
			if(!$class_name || $child instanceof $class_name) {
				$descendants[] = $child;
			}
			if ($deep) {
				foreach ($child->getChildren($class_name, $deep) as $descendant) {
					$descendants[] = $descendant;
				}
			}
		}

		return $descendants;
	}

	/**
	 * @deprecated self::getChildren()
	 * @return array
	 */
// 	function getEntityChildren($path = '', $class_name = false, $deep = false, $throw_exception = true) {
// 		return $this->getChildren($path, $class_name, $deep);
// 	}

	/**
	 * @param string $path
	 * @return bool
	 */
// 	function hasChildren($path = '') {
// 		foreach ($this->storage->scan($path) as $name)
// 			if (($name{0} != '.') && $this->storage->contains("$path/$name/".self::ENTITY_FILENAME))
// 				return true;
// 		return false;
// 	}

	/**
	 * @param string $query
	 * @return array
	 */
// 	function findEntities($query) {
// 		$entities = array();
// 		foreach ($query->execute() as $path)
// 			$entities[] = $this->getEntity($path);
// 		return $entities;
// 	}

	/**
	 * Saves entity state to repository.
	 * Changes become persistent when commit method is called within request.
	 * @param Vivo\CMS\Model\Entity Entity to save.
	 */
	public function saveEntity(\Vivo\CMS\Model\Entity $entity) {
		if (!$entity->getPath()) {
			throw new CMS\Exception(500, 'entity_no_path');
		}


		// @todo: tohle nemelo by vyhazovat exception, misto zmeny path? - nema tohle resit jina metoda, treba pathValidator
		$entityPath = $entity->getPath();
		$path = '';
		$len = strlen($entity->getPath());
		for ($i = 0; $i < $len; $i++) {
			$path.= (stripos('abcdefghijklmnopqrstuvwxyz0123456789-_/.', $entityPath{$i}) !== false)
					? $entityPath{$i} : '-';
		}
		$entity->setPath($path);
		// ---------------------------------------------------------------------

		//if (strpos($entity->path, ' ') !== false || strpos($entity->path, '\t')) //@todo: co je tohle?
// 		if ($entity->security)
// 			CMS::$securityManager->authorize($entity, 'Write');
// 		if (method_exists($entity, 'onSave')) {
// 			$entity->onSave();
// 			CMS::$logger->warn('Method '.get_class($entity).'::onSave() is deprecated. Use Vivo\CMS\Event methods instead.');
// 		}
// 		CMS::$event->invoke(CMS\Event::ENTITY_SAVE, $entity);
		return ($this->saveEntities[$entity->getPath()] = $entity); //@todo: overit co do dela pokud ulozim Xkrat stejnou entitu, zda tohle vraci FALSE.
	}


/**
 * @todo z CMS asi sem
 */
// 	public static function convertURLsToReferences() { }

	/**
	 * No effect - allways transactional.
	 */
	public function begin() {
		trigger_error('No effect - allways transactional.');
	}

	/**
	 * Commit commits the current transaction, making its changes permanent.
	 * @throws Exception
	 */
	public function commit() {
// 		if (CMS::$logger->level >= Logger::LEVEL_FINE)
// 			CMS::$logger->fine('commiting changes');
		$tmp_files = array();
		$tmp_del_files = array();
		try {
			// mazani faze 1 (presun do temp adresare)
			try {
				foreach ($this->deletePaths as $path) {
					$this->storage->move($path, $tmp_del_files[$path] = '/del-'.uniqid());
				}
			}
			catch (\Exception $e) {
				// presun toho co bylo presunuto zpet
				foreach ($tmp_del_files as $path => $tmp_del_path)
					$this->storage->move($tmp_del_path, $path);
				throw $e;
			}
			// ulozeni faze 1 (serializuje entity a soubory do temporarnich souboru)
			/// a) entity
			$now = new \DateTime(); //CMS::$current_time;
// 			$user = CMS::$securityManager->getUserPrincipal();
// 			$username = $user ? "{$user->domain}\\{$user->username}" : Context::$instance->site->domain.'\\'.Security\Manager::USER_ANONYMOUS;

			$username = '__TESTER__'; //@todo: ma repository mit pristup ke secManageru?

			foreach ($this->saveEntities as $entity) {
				if (!$entity->getCreated() instanceof \DateTime) {
					$entity->setCreated($now);
				}
				if (!$entity->getCreatedBy()) {
					$entity->setCreatedBy($username);
				}
				if(!$entity->getUuid()) {
					$entity->setUuid(self::createUuid());
				}

				$entity->setModified($now);
				$entity->setModifiedBy($username);

				$path = $entity->getPath().'/'.self::ENTITY_FILENAME;
				$tmpPath = $path.'.'.uniqid('tmp');

				$this->storage->set($tmpPath, $this->serializer->serialize($entity));

				$tmp_files[$path] = $tmpPath;
			}

			/// b) soubory
// 			foreach ($this->saveFiles as $path => $data) {
// 				$tmp_path = $path.'.'.uniqid('tmp');
// 				$this->storage->set($tmp_path, $data);
// 				$tmp_files[$path] = $tmp_path;
// 			}
// 			foreach ($this->copyFiles as $path => $source) {
// 				$tmp_path = $path.'.'.uniqid('tmp');
// 				if ($this->storage instanceof Util\FS\Local) {
// 					copy($source, $this->storage->root.$tmp_path);
// 				} else {
// 					$this->storage->set($tmp_path, file_get_contents($source)); //TODO optimize
// 				}
// 				$tmp_files[$path] = $tmp_path;
// 			}
			// mazani faze 2
// 			foreach ($tmp_del_files as $tmp_del_file)
// 				$this->storage->remove($tmp_del_file);
			// ulozeni faze 2 (prejmenuje temporarni soubory na skutecne)
			foreach ($tmp_files as $path => $tmpPath) {
				if (!$this->storage->move($tmpPath, $path)) {
					throw new Exception\Exception(sprintf('Commit failed; source: %s, destination: %s', $tmpPath, $path));
				}
			}
			// delete entities from index
// 			foreach ($this->deleteEntities as $path) {
// 				$path = str_replace(' ', '\\ ', $path);
				$query = new Indexer\Query('DELETE Vivo\CMS\Model\Entity\path = :path OR Vivo\CMS\Model\Entity\path = :path/*');
				$query->setParameter('path', $path);

				$this->indexer->execute($query);
// 			}
			foreach ($this->saveEntities as $entity)
				$this->indexer->save($entity); // (re)index entity
			$this->indexer->commit();
			$this->reset();
		} catch (\Exception $e) {
			// doslo k chybe - odmaz vsechny behem commitu vytvorene temporarni soubory
			foreach ($tmp_files as $tmp_path)
				$this->storage->remove($tmp_path);
			// ...a vyprazdni pole spinavych entit a souboru (dela rollback)
			$this->rollback();
			throw $e;
		}
	}

	private function reset() {
		$this->rollback();
	}

	/**
	 * Rollback rolls back the current transaction, canceling its changes.
	 */
	public function rollback() {
		$this->saveEntities = array();
		$this->saveFiles = array();
		$this->deletePaths = array();
		$this->deleteEntities = array();
	}

	/**
	 * @param \Vivo\CMS\Model\Entity $entity
	 * @param unknown_type $name
	 * @param unknown_type $throwException
	 */
	public function getResource(\Vivo\CMS\Model\Entity $entity, $name, $throwException = true) {
// 		if (!$this->storage->contains($path)) {
// 			CMS::$cache->remove($path);
// 			if ($throw_exception)
// 			//@fixme: tohle doresit
// 				throw new CMS\Exception(404, 'file_not_found', array($path)); else
// 				return null;
// 		}
// 		if (CMS::$parameters['cache.resources'] || ($this->storage instanceof Util\FS\DB)) {
// 			if (CMS::$cache->mtime($path) < $this->storage->mtime($path)) {
// 				CMS::$cache->set($path, $this->storage->get($path));
// 			}
// 			return CMS::$cache->root.$path;
// 		} else {
// 			return $this->storage->root.$path;
// 		}
	}

	//@todo ?
// 	public function getResources(Document $doc) { }

	/**
	 * Gets file resources within the given path.
	 * @param string $path Path to resources
	 * @return array
	 */
// 	function getResources($path) {
// 		$resources = array();
// 		foreach ($this->storage->scan($path, Util\FS::FILE) as $name)
// 			if ($name != self::ENTITY_FILENAME)
// 				$resources[] = $name;
// 		return $resources;
// 	}

	/**
	 * Gets file resource directories within the given path.
	 * @param string $path Path to resource directories
	 * @return array
	 *
	 * @todo: pouzitove VXF Processoru, aneb je to k necemu dobre?, docaste private a poresit co s tim.
	 */
	private function getResourceDirectories($path) {
		$resource_directories = array();
		foreach ($this->storage->scan($path, Util\FS::DIR) as $name)
			if (!$this->storage->contains("$path/$name/".self::ENTITY_FILENAME))
				$resource_directories[] = $name;
		return $resource_directories;
	}

	/**
	 * @param string $path
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\Exception 404, File not found
	 */
// 	function readFile($path, $throw_exception = true) {
// 		if ($file = $this->getFile($path, $throw_exception)) {
// 			return file_get_contents($file);
// 		} else {
// 			return false;
// 		}
// 		//TODO vykopirovani a poskytnuti resource souboru z cache, pokud cache.resource = true
// 		return $this->storage->get($path);
// 	}

	/**
	 * @todo: podme ze predavat pouze cestu, zadny data jako takova.
	 */
	public function saveResource(Model\Entity $entity, $name, /*$data*/ $sourceFilePath) {
		$this->writeFile($entity->getPath().'/'.$name, $data);
	}

	/**
	 * @deprecated
	 *
	 * @param string $path
	 * @param mixed $data
	 * @param bool $is_data Change between save and copy.
	 */
	private function writeFile($path, $data, $is_data = true) {
		if ($is_data)
			$this->saveFiles[$path] = $data;
		else
			$this->copyFiles[$path] = $data;
	}

	/**
	 * Podpurna metoda pro Vivo\CMS\Event.
	 * @param Vivo\CMS\Model\Document $entity
	 * @return array
	 */
	public function getAllContents(\Vivo\CMS\Model\Document $document) {
		$return = array();
// 		if($entity instanceof CMS\Model\Document) {

		//@todo:
			$count = $entity->getContentCount();
			for ($index = 1; $index <= $count; $index++) {
				$return = array_merge($return, $entity->getContents($index));
			}
		//--------------

// 		}
		return $return;
	}

	/**
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $event
	 * @param bool $recursive
	 */
// 	private function callEventOn($entity, $event, $recursive = true) {
// 		CMS::$event->invoke($event, $entity);
// 		foreach($this->getAllContents($entity) as $content) {
// 			CMS::$event->invoke($event, $content);
// 		}
// 		if($recursive) {
// 			foreach ($entity->getChildren() as $ch) {
// 				$this->callEventOn($ch, $event, $recursive);
// 			}
// 		}
// 	}

	/**
	 * @param Vivo\CMS\Model\Entity|string $entity Entity object or entity path.
	 * @throws Vivo\CMS\EntityNotFoundException
	 */
	public function deleteEntity(Model\Entity $entity) {
		if (is_string($entity))
			$entity = $this->getEntity($entity);
// 		if (method_exists($entity, 'onDelete')) {
// 			$entity->onDelete();
// 			CMS::$logger->warn('Method '.get_class($entity).'::onDelete() is deprecated. Use Vivo\CMS\Event methods instead.');
// 		}
		CMS::$event->invoke(CMS\Event::ENTITY_DELETE, $entity);
		foreach($this->getAllContents($entity) as $content) {
			CMS::$event->invoke(CMS\Event::ENTITY_DELETE, $content);
		}
		//TODO kontrola, zda je entita prazdna
		$this->deletePaths[] = $this->deleteEntities[] = $entity->path;
	}

	public function deleteResource(Model\Entity $entity, $name) {
		$this->deleteFile($entity->getPath().'/'.$name);
	}

	/**
	 * @param string $path
	 */
	private function deleteFile($path) {
		//TODO kontrola zda to neni entita
		$this->deletePaths[] = $path;
	}

	/**
	 * @todo
	 *
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $target Target path.
	 */
	public function moveEntity(Model\Entity $entity, $target) { }

	/**
	 * @toho
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

	/**
	 * Reindex all entities (contents and childrens) saved under entity.
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param bool $deep
	 */
	public function reindex(Model\Entity $entity, $deep = false/*, $callback = NULL*/) {
// 		if ($callback instanceof \Closure)
// 			$callback($entity);
// 		elseif (is_array($callback)) {
// 			call_user_func($callback, $entity);
// 		}
		$count = 1;
		$this->indexer->save($entity);
		if ($entity instanceof Vivo\CMS\Model\Document) {
			for ($index = 1; $index <= $entity->getContentCount(); $index++)
				foreach ($entity->getContents($index) as $content)
					$count += $this->reindex($content, true, $callback);
		}
		if ($deep)
			foreach ($entity->getChildren() as $child)
				$count += $this->reindex($child, $deep, $callback);
		return $count;
	}

}
