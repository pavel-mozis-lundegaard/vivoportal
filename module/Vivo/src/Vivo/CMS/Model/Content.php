<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Content.php 1927 2012-01-17 12:59:46Z zayda $
 */
namespace Vivo\CMS\Model;

// use Vivo\CMS;
// use Vivo\CMS\Workflow;

/**
 * Base class for all VIVO models.
 *
 * @author tzajicek
 */
class Content extends Entity {

// 	static $DEFAULT_STATE = Workflow::STATE_NEW;
	static $DEFAULT_STATE_CHANGE = array();
	static $DEFAULT_RECURSIVE = false;

	/**
	 * @var string Workflow state
	 * @todo: @see Vivo\CMS\Workflow
	 */
	private $state;
	/**
	 * @var DateTime Date for automatic state change by cron.
	 */
	private $stateChange;
	/**
	 * @var bool
	 */
	private $recursive;

	/**
	 * Setting default values.
	 * @param string $path Entity path.
	 */
	public function __construct($path = null) {
		parent::__construct($path);

// 		$this->state = self::$DEFAULT_STATE;
// 		$this->stateChange = self::$DEFAULT_STATE_CHANGE;
// 		$this->recursive = self::$DEFAULT_RECURSIVE;
	}

	public function getState() {
		return $this->state;
	}

	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * Gets content.
	 * @return Vivo\CMS\Model\Document
	 */
// 	function getDocument() {
// 		if (!$this->__document) {
// 			$path = substr($this->path, 0, strrpos($this->path, '/') - 1);
// 			$path = substr($path, 0, strrpos($path, '/'));
// 			$this->__document = CMS::$repository->getEntity($path);
// 		}
// 		return $this->__document;
// 	}

	/**
	 * Gets content version.
	 * @return int
	 */
	public function getVersion() {
		return ($p = strrpos($this->getPath(), '/')) ? substr($this->getPath(), $p + 1) : 0;
	}

	/**
	 * Sets version.
	 * @param int $version
	 */
	public function setVersion($version) {
		$this->path = substr($this->getPath(), 0, strrpos($this->getPath(), '/') + 1).$version;
	}

	/**
	 * Gets content index. Index means number of a content in the Multicontent Document.
	 * @return int
	 */
	public function getIndex() {
		return preg_match('~\/Contents\.(\d{1,2})\/~', $this->getPath(), $matches) ? $matches[1] : false;
	}

	/**
	 * Returns relative path of the content.
	 * @return string Content relative path
	 */
	public function getRelativePath() {
		return 'Contents'.(($index = $this->getIndex()) ? '.'.$index  : '').'/'.$this->getVersion();
	}

	/**
	 * Icon name.
	 * @return string
	 */
// 	public function getIcon() {
// 		return 'Content';
// 	}

	/**
	 *
	 * @param string $property_name Name of content property referencing entity.
	 * @return null
	 * @since 1.1
	 * @todo implement logic
	 */
// 	function getReferencedEntity($property_name) {
// 		//TODO
// 		return null;
// 	}

	/**
	 *
	 * @param string $property_name Name of content property referencing entity.
	 * @param string|Vivo\CMS\Model\Entity $value Path (site relative) or UUID in [ref:uuid] format or entity.
	 * @since 1.1
	 * @todo implement logic
	 */
// 	function setReferencedEntity($property_name, $value) {
// 		//TODO
// 	}

}
/*
Entity::define_field(__NAMESPACE__.'\Content',
	array(
		'state' => array(
			'length' => 20,
			'index' => true
		),
		'stateChange' => array(
			'index' => true,
			'type' => 'DateTime'
		)
	)
);
*/
