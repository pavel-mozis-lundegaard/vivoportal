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
use Vivo\CMS\Security;
use Vivo\Util;
use Vivo\Logger;

/**
 * Repository class provides methods to works with CMS repository.
 * Repository works transactionally. The saveEntity or deleteEntity statement begins a new transaction. Commit method commits the current transaction, making its changes permanent.
 * Rollback rolls back the current transaction, canceling its changes.
 * @author tzajicek
 * @version 1.0
 */
class Repository implements RepositoryInterface, Vivo\TransactionalInterface {

	const ENTITY_FILENAME = 'Entity.object'; //dat taky do storage?

	/**
	 * @var Vivo\Util\FS
	 */
	private $storage;
	/**
	 * @var Vivo\CMS\Solr\Indexer
	 */
	private $indexer;

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
	 * @param Vivo\CMS\StorageInterface $storage
	 * @param Vivo\CMS\IndexerInterface $indexer
	 */
	public function __construct(StorageInterface $storage, IndexerInterface $indexer) {
		$this->storage = $storage;
		$this->indexer = $indexer;

// 		switch ($storage_classname = CMS::$parameters['repository.storage_class']) {
// 			case 'Vivo\Util\FS\Local':
// 				$this->storage =
// 					new Util\FS\Local(
// 						($repository_base = CMS::$parameters['repository.base']) ?
// 							$repository_base :
// 							VIVO_BASE.'/Sites');
// 				break;
// 			case 'Vivo\Util\FS\DB':
// 				$this->storage =
// 					new Util\FS\DB(Context::$instance->db, 'storage');
// 				break;
// 			default:
// 				$this->storage = new $storage_classname;
// 		}
// 		$this->indexer = new CMS\Solr\Indexer($this); // new CMS\DB\Indexer($this);
	}

	/**
	 * Returns entity from CMS repository by its identification.
	 * @param string $ident entity identification (path, UUID or symbolic reference)
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
	 */
	public function getEntity($ident, $throw_exception = true) {
		$uuid = null;
		if (preg_match('/^\[ref:('.CMS\Model\Entity::UUID_PATTERN.')\]$/i', $ident, $matches)) {
			// symbolic reference in [ref:uuid] format
			$uuid = strtoupper($matches[1]);
		} elseif (preg_match('/^'.CMS\Model\Entity::UUID_PATTERN.'$/i', $ident)) {
			// UUID
			$uuid = strtoupper($ident);
		}
		if ($uuid) {
			/* UUID */
			$entities = $this->indexer->query("vivo_cms_model_entity_uuid:$uuid");
			if (!empty($entities)) {
				if ((count($entities) > 1) && (CMS::$logger->level >= Logger::LEVEL_WARN)) {
					$paths = array();
					foreach ($entities as $entity)
						$paths[] = $entity->path;
					CMS::$logger->warn("multiple entities with the same UUID $uuid detected (".implode(', ', $paths).')');
				}
				return $entities[0];
			}
			if ($throw_exception)
				throw new CMS\EntityNotFoundException($uuid);
			return null;
		} else {
			/* path */
			$ident = str_replace('//', '/', $ident); //s prechodem na novy zapis cest se muze vyskytnout umisteni 2 lomitek
			$path = $ident.((substr($ident, -1) == '/') ? '' : '/').self::ENTITY_FILENAME;
			$cache_mtime = CMS::$cache->mtime($path);
			if (($cache_mtime == Util\FS\Cache::NOW) || (($storage_mtime = $this->storage->mtime($path)) && ($cache_mtime > $storage_mtime))) {
				// cache access
				$entity = CMS::$cache->get($path, false, true);
			} else {
				// repository access
				CMS::$cache->remove($path); // first remove entity from 2nd level cache
				if (!$this->storage->contains($path)) {
					if ($throw_exception)
						throw new CMS\EntityNotFoundException(substr($path, 0, -strlen(self::ENTITY_FILENAME)));
					return null;
				}
				if (CMS::$logger->level >= Logger::LEVEL_FINER)
					CMS::$logger->finer("getting entity $path from repository");
				try {
					$entity = Util\Object::unserialize($this->storage->get($path));
				} catch (\Exception $e) {
					if ($throw_exception)
						throw $e;
					return null;
				}
				CMS::$cache->set($path, $entity, true);
			}
			$entity = CMS::convertReferencesToURLs($entity);
			$entity->setPath(substr($path, 0, strrpos($path, '/'))); // set volatile path property of entity instance

			// 1.2 backward compatibility issue (added by zayda)
// 			if ($entity->sorting == 'vivo_cms_model_document_position asc' ||
// 				$entity->sorting == 'vivo_cms_model_document_position desc')
// 				$entity->sorting = str_replace('document', 'folder', $entity->sorting);

			return $entity;
		}
	}

	/**
	 * Returns entity from CMS repository by its UUID or symbolic reference.
	 * @deprecated use getEntity() method instead
	 * @param string $ident entity UUID or symbolic reference
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
	 */
// 	function getEntityByUUID($uuid, $throw_exception = true) {
// 		return $this->getEntity($uuid, $throw_exception);
// 	}

	/**
	 * Convert entity UUID to repository path.
	 * @author mhajek
	 * @param string $uuid		Entity UUID.
	 * @return string|null		Entity path.
	 */
	public function getEntityPathByUuid($uuid) {
// 	public function getEntityPathByUUID($uuid) {
		$paths = array();
		$facetPaths = $this->indexer->query("vivo_cms_model_entity_uuid:$uuid",
										array(
											'limit' => 0,
											'facet' => 'on',
											'facet.field' => array('vivo_cms_model_entity_path'),
											'facet.limit' => 2 // Nemuze byt maximum, bohuzel existuje mnoho duplicitnich UUID na starych webech.
										),
										CMS\Solr\Indexer::FACET_FIELD_LIST);

		foreach ($facetPaths as $path => $count) {
			if($count) $paths[] = $path;
		}

		if (count($paths) > 1 && (CMS::$logger->level >= Logger::LEVEL_WARN)) {
			CMS::$logger->warn("Multiple entities with the same UUID $uuid detected (".implode(', ', $paths).')');
		}

		return isset($paths[0]) ? $paths[0] : null;
	}

	/**
	 * Return subdocuments.
	 * @param string $path Entity path in CMS repository.
	 * @param string $class_name Class name for filtering.
	 * @param int $deep
	 * @return array
	 */
	function getChildren($path = '', $class_name = false, $deep = false, $throw_exception = true) {
		$children = array();
		$descendants = array();

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
				if ($this->storage->contains("$path/$name/".self::ENTITY_FILENAME)) {
					//echo "$child_path<br>";
					$entity = $this->getEntity($child_path, $throw_exception);
					if ($entity && ($entity instanceof CMS\Model\Site || CMS::$securityManager->authorize($entity, 'Browse', false)))
						$children[] = $entity;
				}
			}
		}
		// sorting
		$entity = $this->getEntity($path, false);
		if ($entity instanceof CMS\Model\Entity) {
			$sorting = method_exists($entity, 'sorting') ? $entity->sorting() : $entity->sorting;
			if (Util\Object::is_a($sorting, 'Closure')) {
				usort($children, $sorting);
			} elseif (is_string($sorting)) {
				$cmp_function = 'cmp_'.str_replace(' ', '_', ($rpos = strrpos($sorting, '_')) ? substr($sorting, $rpos + 1) : $sorting);
				if (method_exists($entity, $cmp_function)) {
					usort($children, array($entity, $cmp_function));
				}
			}
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
	function getEntityChildren($path = '', $class_name = false, $deep = false, $throw_exception = true) {
		return $this->getChildren($path, $class_name, $deep);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	function hasChildren($path = '') {
		foreach ($this->storage->scan($path) as $name)
			if (($name{0} != '.') && $this->storage->contains("$path/$name/".self::ENTITY_FILENAME))
				return true;
		return false;
	}

	/**
	 * Returns site entity by host name.
	 * @param string $host
	 * @return Vivo\CMS\Model\Site|null
	 */
	function getSiteByHost($host) {
		foreach ($this->getChildren('', false, false, false) as $site) {
			if (in_array($host, $site->hosts))
				return $site;
		}
		return null;
	}

	/**
	 * @param string $query
	 * @return array
	 */
	function findEntities($query) {
		$entities = array();
		foreach ($query->execute() as $path)
			$entities[] = $this->getEntity($path);
		return $entities;
	}

	/**
	 * Saves entity state to repository.
	 * Changes become persistent when commit method is called within request.
	 * @param Vivo\CMS\Model\Entity Entity to save.
	 */
	function saveEntity($entity) {
		if (!$entity->path)
			throw new CMS\Exception(500, 'entity_no_path');
		$path = '';
		for ($i = 0; $i < strlen($entity->path); $i++)
			$path .=
				(stripos('abcdefghijklmnopqrstuvwxyz0123456789-_/.', $entity->path{$i}) !== false) ?
					$entity->path{$i} : '-';
		$entity->path = $path;
		if (strpos($entity->path, ' ') !== false || strpos($entity->path, '\t'))
		if ($entity->security)
			CMS::$securityManager->authorize($entity, 'Write');
		if (method_exists($entity, 'onSave')) {
			$entity->onSave();
			CMS::$logger->warn('Method '.get_class($entity).'::onSave() is deprecated. Use Vivo\CMS\Event methods instead.');
		}
		CMS::$event->invoke(CMS\Event::ENTITY_SAVE, $entity);
		return ($this->saveEntities[$entity->path] = $entity);
	}

	function begin() {
		// no effect - allways transactional
	}

	/**
	 * Commit commits the current transaction, making its changes permanent.
	 * @throws Exception
	 */
	function commit() {
		if (CMS::$logger->level >= Logger::LEVEL_FINE)
			CMS::$logger->fine('commiting changes');
		$tmp_files = array();
		$tmp_del_files = array();
		try {
			// mazani faze 1 (presun do temp adresare)
			try {
				foreach ($this->deletePaths as $path)
					$this->storage->move($path, $tmp_del_files[$path] = '/Temp/del-'.uniqid());
			} catch (\Exception $e) {
				// presun toho co bylo presunuto zpet
				foreach ($tmp_del_files as $path => $tmp_del_path)
					$this->storage->move($tmp_del_path, $path);
				throw $e;
			}
			// ulozeni faze 1 (serializuje entity a soubory do temporarnich souboru)
			/// a) entity
			$now = CMS::$current_time;
			$user = CMS::$securityManager->getUserPrincipal();
			$username = $user ? "{$user->domain}\\{$user->username}" : Context::$instance->site->domain.'\\'.Security\Manager::USER_ANONYMOUS;
			foreach ($this->saveEntities as $entity) {
				if (!is_object($entity->created))
					$entity->created = $now;
				if (!$entity->createdBy)
					$entity->createdBy = $username;
				$entity->modified = $now;
				$entity->modifiedBy = $username;
				$path = $entity->path.'/'.self::ENTITY_FILENAME;
				$tmp_path = $path.'.'.uniqid('tmp');
				CMS::$cache->remove($path);
				$this->storage->set($tmp_path, Util\Object::serialize(CMS::convertURLsToReferences($entity)));
				$tmp_files[$path] = $tmp_path;
				//TODO overit, zda ma prava na prepsani (move) souboru v $path
			}
			/// b) soubory
			foreach ($this->saveFiles as $path => $data) {
				$tmp_path = $path.'.'.uniqid('tmp');
				$this->storage->set($tmp_path, $data);
				$tmp_files[$path] = $tmp_path;
			}
			foreach ($this->copyFiles as $path => $source) {
				$tmp_path = $path.'.'.uniqid('tmp');
				if ($this->storage instanceof Util\FS\Local) {
					copy($source, $this->storage->root.$tmp_path);
				} else {
					$this->storage->set($tmp_path, file_get_contents($source)); //TODO optimize
				}
				$tmp_files[$path] = $tmp_path;
			}
			// mazani faze 2
			foreach ($tmp_del_files as $tmp_del_file)
				$this->storage->remove($tmp_del_file);
			// ulozeni faze 2 (prejmenuje temporarni soubory na skutecne)
			foreach ($tmp_files as $path => $tmp_path) {
				if (CMS::$logger->level >= Logger::LEVEL_FINER)
					CMS::$logger->finer("commit $path");
				if (!$this->storage->move($tmp_path, $path))
					throw new CMS\Exception(500, 'commit_failed', array($tmp_path, $path));
			}
			// delete entities from index
			foreach ($this->deleteEntities as $path) {
				$path = str_replace(' ', '\\ ', $path);
				$this->indexer->deleteByQuery("vivo_cms_model_entity_path:$path OR vivo_cms_model_entity_path:$path/*");
			}
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

	/**
	 * Rollback rolls back the current transaction, canceling its changes.
	 * @see self::reset()
	 */
	function rollback() {
// 		$this->reset();

		$this->saveEntities = array();
		$this->saveFiles = array();
		$this->deletePaths = array();
		$this->deleteEntities = array();
	}

	/**
	 * Support function. Reset rolls back the current transaction.
	 */
// 	function reset() {
// 		$this->saveEntities = array();
// 		$this->saveFiles = array();
// 		$this->deletePaths = array();
// 		$this->deleteEntities = array();
// 	}

	/**
	 * @param string $path
	 * @param bool $throw_exception
	 * @return string|null File path.
	 * @throws Vivo\CMS\Exception 404, File not found
	 */
	function getFile($path, $throw_exception = true) {
		if (!$this->storage->contains($path)) {
			CMS::$cache->remove($path);
			if ($throw_exception)
			//@fixme: tohle doresit
				throw new CMS\Exception(404, 'file_not_found', array($path)); else
				return null;
		}
		if (CMS::$parameters['cache.resources'] || ($this->storage instanceof Util\FS\DB)) {
			if (CMS::$cache->mtime($path) < $this->storage->mtime($path)) {
				CMS::$cache->set($path, $this->storage->get($path));
			}
			return CMS::$cache->root.$path;
		} else {
			return $this->storage->root.$path;
		}
	}

	/**
	 * Gets file resources within the given path.
	 * @param string $path Path to resources
	 * @return array
	 */
	function getResources($path) {
		$resources = array();
		foreach ($this->storage->scan($path, Util\FS::FILE) as $name)
			if ($name != self::ENTITY_FILENAME)
				$resources[] = $name;
		return $resources;
	}

	/**
	 * Gets file resource directories within the given path.
	 * @param string $path Path to resource directories
	 * @return array
	 */
	function getResourceDirectories($path) {
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
	function readFile($path, $throw_exception = true) {
		if ($file = $this->getFile($path, $throw_exception)) {
			return file_get_contents($file);
		} else {
			return false;
		}
		//TODO vykopirovani a poskytnuti resource souboru z cache, pokud cache.resource = true
		return $this->storage->get($path);
	}

	/**
	 * @param string $path
	 * @param mixed $data
	 * @param bool $is_data Change between save and copy.
	 */
	function writeFile($path, $data, $is_data = true) {
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
	public function getAllContents($entity) {
		$return = array();
		if($entity instanceof CMS\Model\Document) {
			$count = $entity->getContentCount();
			for ($index = 1; $index <= $count; $index++) {
				$return = array_merge($return, $entity->getContents($index));
			}
		}
		return $return;
	}

	/**
	 * @param Vivo\CMS\Model\Entity $entity
	 * @param string $event
	 * @param bool $recursive
	 */
	private function callEventOn($entity, $event, $recursive = true) {
		CMS::$event->invoke($event, $entity);
		foreach($this->getAllContents($entity) as $content) {
			CMS::$event->invoke($event, $content);
		}
		if($recursive) {
			foreach ($entity->getChildren() as $ch) {
				$this->callEventOn($ch, $event, $recursive);
			}
		}
	}

	/**
	 * @param Vivo\CMS\Model\Entity|string $entity Entity object or entity path.
	 * @throws Vivo\CMS\EntityNotFoundException
	 */
	function deleteEntity($entity) {
		if (is_string($entity))
			$entity = $this->getEntity($entity);
		if (method_exists($entity, 'onDelete')) {
			$entity->onDelete();
			CMS::$logger->warn('Method '.get_class($entity).'::onDelete() is deprecated. Use Vivo\CMS\Event methods instead.');
		}
		CMS::$event->invoke(CMS\Event::ENTITY_DELETE, $entity);
		foreach($this->getAllContents($entity) as $content) {
			CMS::$event->invoke(CMS\Event::ENTITY_DELETE, $content);
		}
		//TODO kontrola, zda je entita prazdna
		$this->deletePaths[] = $this->deleteEntities[] = $entity->path;
	}

	/**
	 * @param string $path
	 */
	function deleteFile($path) {
		//TODO kontrola zda to neni entita
		$this->deletePaths[] = $path;
	}

	/**
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 */
	function move($path, $target) {
		if (strpos($target, "$path/") === 0)
			throw new CMS\Exception(500, 'recursive_operation', array($path, $target));
		$path2 = str_replace(' ', '\\ ', $path);
		$this->indexer->deleteByQuery("vivo_cms_model_entity_path:$path2 OR vivo_cms_model_entity_path:$path2/*");
		$entity = $this->getEntity($path);
		if (method_exists($entity, 'beforeMove')) {
			$entity->beforeMove($target);
			CMS::$logger->warn('Method '.get_class($entity).'::geforeMove() is deprecated. Use Vivo\CMS\Event methods instead.');
		}
		$this->callEventOn($entity, CMS\Event::ENTITY_BEFORE_MOVE);
		$this->storage->move($path, $target);
		$targetEntity = $this->getEntity($target);
		$targetEntity->path = rtrim($target, '/'); // @fixme: tady by melo dojit nejspis ke smazani kese, tak aby nova entita mela novou cestu a ne starou
		$this->callEventOn($targetEntity, CMS\Event::ENTITY_AFTER_MOVE);
		//CMS::$cache->clear_mem(); //@fixme: Dodefinovat metodu v Cache tridach - nestaci definice v /FS/Cache, ale i do /FS/DB/Cache - definovat ICache
		$this->reindex($targetEntity, true);
		$this->indexer->commit();
		return $targetEntity;
	}

	/**
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 * @throws Vivo\CMS\Exception 500, Recursive operation
	 */
	function copy($path, $target) {
		if (strpos($target, "$path/") === 0)
			throw new CMS\Exception(500, 'recursive_operation', array($path, $target));
		$entity = $this->getEntity($path);
		CMS::$securityManager->authorize($entity, 'Copy');
		if (method_exists($entity, 'beforeCopy')) {
			$entity->beforeCopy($target);
			CMS::$logger->warn('Method '.get_class($entity).'::geforeCopy() is deprecated. Use Vivo\CMS\Event methods instead.');
		}
		$this->callEventOn($entity, CMS\Event::ENTITY_BEFORE_COPY);
		$this->storage->copy($path, $target);
		if ($entity = $this->getEntity($target, false)) {
			if ($entity->title)
				$entity->title .= ' COPY';
			$this->copy_entity($entity);
			$this->commit();
			$this->reindex($entity);
		}
		return $entity;
	}

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
		if (method_exists($entity, 'afterCopy')) {
			$entity->afterCopy();
			CMS::$logger->warn('Method '.get_class($entity).'::afterCopy() is deprecated. Use Vivo\CMS\Event methods instead.');
		}
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
	function reindex($entity, $deep = false, $callback = NULL) {
		if ($callback instanceof \Closure)
			$callback($entity);
		elseif (is_array($callback)) {
			call_user_func($callback, $entity);
		}
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
