<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Entity.php 2150 2012-08-15 11:05:23Z mhajek $
 */
namespace Vivo\CMS\Model;

use Vivo;
use Vivo\Util;
use Vivo\Converter;
use Vivo\CMS;
use Vivo\CMS\Model\Entity\Lock;

class Entity {
	/**
	 * Universally Unique Identifier (UUID) of the entity instance.
	 * Value is set when entity is being instantiated. Never set or change value of this property.
	 * @see __construct
	 * @var string
	 */
	private $uuid;
	/**
	 * Absolute path to entity stored in repository.
	 * @var string
	 */
	private $path;
	/**
	 * Not used yet.
	 * @var Vivo\CMS\Model\Lock
	 */
// 	protected $lock;
	/**
	 * If TRUE, entity will be indexed by fulltext indexer.
	 * @see Vivo\CMS\Solr\Indexer
	 * @var boolean
	 */
	private $searchable;
	/**
	 * Time of entity creation.
	 * @var DateTime
	 */
	private $created;
	/**
	 * Username of entity creator.
	 * @var string
	 */
	private $createdBy;
	/**
	 * Time of entity last modification.
	 * @var DateTime
	 */
	private $modified;
	/**
	 * Username of user who made last last modification.
	 * @var string
	 */
	private $modifiedBy;
	/**
	 * Constructor. Sets uuid property by value obtained from static method create_uuid().
	 * @param string $path Path to entity. If not set, it will be undefined and can be set later before persisting entity using saveEntity method of Repository.
	 * @see Vivo\CMS\DAO\Repository::saveEntity()
	 */
	public function __construct($path = null) {
		$this->path = $path;
// 		if ($path)
// 			$this->setPath($path);
// 		$this->uuid = self::create_uuid();
// 		$this->searchable = self::$DEFAULT_SEARCHABLE;
	}

	/**
	* Compare entities by path.
	* @param Vivo\CMS\Model\Entity $entity
	* @return bool Returns true if the entity is under another entity (in the tree paths).
	*/
	public function under($entity) {
		return (strpos($this->path.'/', $entity->path.'/') === 0);
	}

	public function setUuid($uuid) {
		$this->uuid = $uuid;
	}

	public function getUuid() {
		return $this->uuid;
	}

	/**
	 * Returns parent entity if $name is 'parent'.
	 * @param string $name
	 */
// 	function __get($name) {
// 		if ($name == 'parent') {
// 			return $this->getParent();
// 		}

// 		return isset($this->$name) ? $this->$name : null;
// 	}

	/**
	 * Creates new Entity uuid after an object cloning.
	 */
// 	public function __clone() {
// 		$this->uuid = self::createUuid();
// 	}



	/**
	 * Sets entity path.
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Gets entity path.
	 * @param string $subpath Subpath will be added to the end.
	 * @return string
	 */
	public function getPath(/*$subpath = ''*/) {
		return $this->path/*.($subpath ? "/$subpath" : '')*/;
	}

	/**
	 * Gets entity name.
	 * @return string
	 */
	public function getName() {
		return (($pos = strrpos($this->path, '/')) !== false) ? substr($this->path, $pos + 1) : '';
	}

	/**
	 * @param DateTime $date
	 */
	public function setCreated(\DateTime $date) {
		$this->created = $date;
	}

	/**
	 * @return DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return string $userName
	 */
	public function setCreatedBy($userName) {
		$this->createdBy = $userName;
	}

	/**
	 * @return string
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}

	/**
	 * @param DateTime $date
	 */
	public function setModified(\DateTime $date) {
		$this->modified = $date;
	}

	/**
	 * @return DateTime
	 */
	public function getModified() {
		return $this->modified;
	}

	/**
	 * @return string $userName
	 */
	public function setModifiedBy($userName) {
		$this->modifiedBy = $userName;
	}

	/**
	 * @return string
	 */
	public function getModifiedBy() {
		return $this->modifiedBy;
	}



	/**
	 * Returns entity from CMS repository by path.
	 * @param string $path
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
	 */
// 	function getEntity($path, $throw_exception = true) {
// 		return CMS::$repository->getEntity($this->getPath($path), $throw_exception);
// 	}

	/**
	 * Gets parent entity.
	 * @return Vivo\CMS\Model\Entity|null
	 */
// 	function getParent() {
// 		return
// 			($pos = strrpos($this->path, '/')) ?
// 				CMS::$repository->getEntity(substr($this->path, 0, $pos), false) :
// 				null;
// 	}

	/**
	 * Gets site enity ~ root entity of this.
	 * @return Vivo\CMS\Model\Entity|null
	 */
// 	function getSite() {
// 		return
// 			($pos = strpos($this->path, '/', 1)) ?
// 				CMS::$repository->getEntity(substr($this->path, 0, $pos), false) :
// 				null;
// 	}

// 	function getResources() {
// 		return CMS::$repository->getResources($this->path);
// 	}

	/**
	 * @param string $class_name
	 */
// 	function getChildren($class_name = false, $deep = false) {
// 		return CMS::$repository->getChildren($this->path, $class_name, $deep);
// 	}

	/**
	 * @return bool
	 */
// 	function hasChildren() {
// 		return CMS::$repository->hasChildren($this->path);
// 	}

	/**
	 * @return string
	 */
	public function __toString() {
		return get_class($this).'{uuid: '.$this->uuid.', path: '.$this->path.'}';
	}

	/**
	 * @return array
	 */
// 	function __vivo_sleep() {
// 		//if (!$this->uuid)
// 		//	$this->uuid = strtoupper(md5(uniqid()));
// 		$vars = get_object_vars($this);
// 		unset($vars['path']);
// 		return array_keys($vars);
// 	}

	/**
	 * @comp drive getURL
	 * @author tzajicek
	 * @param string $subpath
	 * @param bool $ignore_specific_url
	 * @return string URL.
	 */
// 	public function getUrl(/*$subpath = false,*/ $ignore_specific_url = false) {
// 		// get url
// 		$url =
// 			(!$ignore_specific_url && $this->url_precedence && $this->url) ?
// 				$this->url :
// 				//@todo: nahradit za self::getUrlFromPath() - ale ted se tady v tom moc nevyznam, tak az v 2.0
// 				((($pos = strpos($this->path, '/ROOT')) ?
// 					substr($this->path, $pos + 5) :
// 					'').
// 				'/'); // neresi URL mimo kontext site
// 		// append subpath
// // 		if ($subpath) {
// // 			if (substr($url, -1) == '/') {
// // 				$url .= ($subpath{0} == '/') ? substr($subpath, 1) : $subpath;
// // 			} else {
// // 				$url .= ($subpath{0} == '/') ? $subpath : '/'.$subpath;
// // 			}
// // 		}
// 		return $url;
// 	}

	/**
	 * Returns URL from entity path.
	 * @param string $path
	 * @return string
	 */
// 	public static function getUrlFromPath($path) {
// 		$url = substr($path, strpos($path, '/ROOT') + 5).'/';

// 		return $url;
// 	}

	/**
	 * Depth in the repository tree
	 * @return int
	 */
	public function getDepth() {
		if (!$this->path)
			return false;
		return substr_count($this->path, '/', 1);
	}

	/**
	 * @return string
	 */
// 	public function getIcon() {
// 		return 'Entity';
// 	}

	/**
	 * Returns string for full-text. UUID and created by.
	 * @param array $field_names Field names will be indexed.
	 * @return string
	 */
	public function getTextContent($field_names = array()) {
		$text = "[self:{$this->uuid}]";
		$field_names = array_unique($field_names);
		foreach ($field_names as $name) {
			$value = $this->$name;
			$type = is_object($value) ? get_class($value) : gettype($value);
			if ($value && ($converter = Converter\Factory::get($type, false)))
				$text.= ' '.$converter->convert('string', $value, 'en_US');
		}
		return $text;
	}

	/**
	 * @see Entity::$FIELDS
	 * @param bool $per_class Sorting.
	 * @return array
	 */
// 	function getDescriptors($per_class = false) {
// 		$descriptors = array();
// 		$class_name = get_class($this);
// 		do {
// 			if (isset(self::$FIELDS[$class_name]) && count(self::$FIELDS[$class_name])) {
// 				$class_descriptors = self::$FIELDS[$class_name];
// 				foreach ($class_descriptors as $name => $descriptor)
// 					$class_descriptors[$name]['name'] = "$class_name\\$name";
// 				if ($per_class)
// 					$descriptors[$class_name] = $class_descriptors;
// 				else
// 					$descriptors = array_merge($class_descriptors, $descriptors);
// 			}
// 			if ($class_name == __CLASS__)
// 				break;
// 			$class_name = get_parent_class($class_name);
// 		} while ($class_name);
// 		return $per_class ? array_reverse($descriptors, true) : $descriptors;
// 	}

	/**
	 * Vsechno tohle do b. logiky / app vrstva
	 *
	 * @author tzajicek
	 */
// 	function getReferencingEntities() {
// 		//echo '\\[ref\\:'.$this->uuid.'\\]';
// 		return CMS::$repository->indexer->query('\\[ref\\:'.$this->uuid.'\\]');
// 	}

	/**
	 * Creates lock on this entity, is used in backend.
	 * @see self::unlock()
	 * @param string $username
	 * @param string $lock_type
	 */
// 	function lock($username, $lock_type){
// 		$this->lock = new Entity\Lock($username);
// 		$this->lock->lock_type = $lock_type;
// 	}

	/**
	 * Removes lock.
	 * @see self::lock()
	 */
// 	function unlock(){
// 		unset($this->lock);
// 	}

	/**
	 * Defines new entity descriptor in $FIELDS array.
	 * @param string $class_name Entity class name.
	 * @param string $field_name Field name.
	 * @param array $parameters
	 */
// 	static function define_field($class_name, $field_name_or_defs, $parameters = NULL) {
// 		if (!class_exists($class_name))
// 			Vivo::load($class_name);
// 		if (!isset(self::$FIELDS[$class_name]))
// 			self::$FIELDS[$class_name] = array();
// 		if (is_array($field_name_or_defs))
// 			self::$FIELDS[$class_name] = $field_name_or_defs; else
// 			self::$FIELDS[$class_name][$field_name_or_defs] = $parameters;
// 	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Entity $doc1
	 * @param Vivo\CMS\Model\Entity $doc2
	 * @return int
	 */
	static function cmp_created_asc($doc1, $doc2) {
		return $doc1->created >= $doc2->created ?
			$doc1->created == $doc2->created ? 0 : 1 : -1;
	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Entity $doc1
	 * @param Vivo\CMS\Model\Entity $doc2
	 * @return int
	 */
	static function cmp_created_desc($doc1, $doc2) {
		return $doc1->created <= $doc2->created ?
			$doc1->created == $doc2->created ? 0 : 1 : -1;
	}

	/**
	 * Returns front component full class name.
	 * @see FRONT_COMPONENT
	 * @return string|false Returns false if front class is not defined.
	 */
// 	function getFrontComponentClass() {
// 		return $this->getComponentClass('FRONT');
// 	}

	/**
	 * Returns editor component full class name.
	 * @see EDITOR_COMPONENT
	 * @return string|false Returns false if editor class is not defined.
	 */
// 	function getEditorComponentClass() {
// 		return $this->getComponentClass('EDITOR');
// 	}

	/**
	 * Returns component class name.
	 * @param string $type	Component type / name.
	 * @return string|false	Returns false if component class is not defined.
	 */
// 	function getComponentClass($type) {
// 		// defined as member field
// 		$component_class_field = strtolower($type).'_component';
// 		if ($component_class = $this->$component_class_field)
// 			return $component_class;
// 		// defined as constant
// 		$component_class_constant = strtoupper($type).'_COMPONENT';
// 		$entity_class = Util\Object::get_class($this);
// 		while ($entity_class != 'Vivo\CMS\Model\Entity') {
// 			if ($component_class = Util\Object::constant($entity_class, $component_class_constant))
// 				return $component_class;
// 			$entity_class = Util\Object::get_parent_class($entity_class);
// 		}
// 		// not defined
// 		return false;
// 	}

	/**
	 * Compares if this content is logically equivalent to another content.
	 * This implementation compares only properties defined via $FIELDS.
	 * @param Vivo\CMS\Model\Entity $entity
	 * @return bool
	 */
	public function equals($entity) {
		$this_class = get_class($this);
		$content_class = get_class($entity);
		if ($this_class != $content_class)
			return false;
		//@todo: musim mit pristup k field descriptorum :/
// 		foreach (Entity::$FIELDS[$this_class] as $name => $descriptor)
// 			if ($descriptor['comparable'] && ($this->$name != $entity->$name))
// 				return false;
		return true;
	}

}
/*
Entity::$FIELDS[__NAMESPACE__.'\Entity'] =
	array(
		'path' => array(
			'type' => 'string',
			//'important' => true,
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => true,
			'order' => 1
		),
		'uuid' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'length' => 50,
			'index' => true,
			'order' => 2
		),
		'type' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'length' => 100,
			'index' => true,
			'order' => 3
		),
		'lock' => array(
			'type' => 'boolean',
			'field-type' => false/ *'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => true,
			'order' => 4* /
		),
		'created' => array(
			'type' => 'DateTime',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => true,
			'order' => 1000
		),
		'createdBy' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'length' => 50,
			'index' => true,
			'order' => 1002
		),
		'modified' => array(
			'type' => 'DateTime',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => true,
			'order' => 1004
		),
		'modifiedBy' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'length' => 50,
			'index' => true,
			'order' => 1005
		)
	);
*/
