<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Site.php 1927 2012-01-17 12:59:46Z zayda $
 */
namespace Vivo\CMS\Model;

use Vivo\CMS;
use Vivo\Util\Messages;

/**
 * Represents web site as VIVO model.
 * @since 1.0
 * @author tzajicek
 */
class Site extends Folder {

// 	static $DEFAULT_TITLE;
// 	static $DEFAULT_DOMAIN;
// 	static $DEFAULT_PARENT_SITE;
// 	static $DEFAULT_METAS = array();
// 	static $DEFAULT_HOSTS = array();
// 	static $DEFAULT_LINKS = array();
// 	static $DEFAULT_SCRIPTS = array();
// 	static $DEFAULT_CONTENT_TYPES = array();
// 	static $DEFAULT_ENTITY_TYPES = array();
// 	static $DEFAULT_EDITOR_BLOCKFORMATS = 'p,h2,h3,h4';
// 	static $DEFAULT_EDITOR_STYLES;

	/**
	 * @var string Security domain name.
	 */
	protected $domain;
	/**
	 * @var string Parent site name.
	 * @example META-SITE
	 */
	protected $parentSite;
	/**
	 * @var array Hosts are domain address under which you accessed the site.
	 */
	protected $hosts = array();

	/**
	 * @param string Path to entity.
	 * @param Vivo\CMS\Model\Entity\Security
	 */
	function __construct($path = null, $security = null) {
		parent::__construct($path, $security);
// 		$this->title = self::$DEFAULT_TITLE;
// 		$this->domain = self::$DEFAULT_DOMAIN;
// 		$this->parent_site = self::$DEFAULT_PARENT_SITE;
// 		$this->metas = self::$DEFAULT_METAS;
// 		$this->hosts = self::$DEFAULT_HOSTS;
// 		$this->links = self::$DEFAULT_LINKS;
// 		$this->scripts = self::$DEFAULT_SCRIPTS;
// 		$this->content_types = self::$DEFAULT_CONTENT_TYPES;
// 		$this->entity_types = self::$DEFAULT_ENTITY_TYPES;
// 		$this->editor_blockformats = self::$DEFAULT_EDITOR_BLOCKFORMATS;
// 		$this->editor_styles = self::$DEFAULT_EDITOR_STYLES;
	}

	public function setDomain($domain) {
		$this->domain = $domain;
	}

	public function setHosts(array $hosts) {
		$this->hosts = $hosts;
	}

	/**
	 * Get entity from site repository
	 * @param string $path
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\EntityNotFoundException
	 * @return Vivo\CMS\Model\Entity
	 */
// 	function getEntity($path, $throw_exception = true) {
// 		if (substr($path, 0, 1) == '/') {
// 			return CMS::$repository->getEntity($path, $throw_exception);
// 		} else if ($this->parent_site) {
// 			if ($entity = CMS::$repository->getEntity($this->path.'/'.$path, false))
// 				return $entity;
// 			return CMS::$repository->getEntity('/'.$this->parent_site)->getEntity($path, $throw_exception);
// 		} else {
// 			return CMS::$repository->getEntity($this->path.'/'.$path, $throw_exception);
// 		}
// 	}

	/**
	 * Returns document from repository by URL.
	 * @param string URL
	 * @return Vivo\CMS\Model\Document|null
	 */
// 	function getDocumentByURL($url) {
// 		if ($document = CMS::$repository->getEntity(rtrim($this->path.'/ROOT'.$url, '/'), false)) {
// 			return $document;
// 		}
// 		if ($url && ($url != '/')) {
// 			$query = "(vivo_cms_model_entity_path:{$this->path}/ and vivo_cms_model_document_url:\"$url\")";
// 			//die($query);
// 			if ($documents = CMS::$repository->indexer->query($query))
// 				return $documents[0];
// 		}
// 		if ($this->parent_site) {
// 			return CMS::$repository->getEntity('/'.$this->parent_site)->getDocumentByURL($url);
// 		}
// 		return null;
// 	}

	/**
	 * @param string
	 * @param bool
	 * @throws Vivo\CMS\Exception 404, File not found
	 * @return string
	 */
// 	function getFile($path, $throw_exception = true) {
// 		if (substr($path, 0, 1) == '/') {
// 			return CMS::$repository->getFile($path, $throw_exception);
// 		} else if ($this->parent_site) {
// 			if ($file = CMS::$repository->getFile($this->path.'/'.$path, false))
// 				return $file;
// 			return CMS::$repository->getEntity('/'.$this->parent_site)->getFile($path, $throw_exception);
// 		} else {
// 			return CMS::$repository->getFile($this->path.'/'.$path, $throw_exception);
// 		}
// 	}

	/**
	 * Returns parent site
	 * @return Vivo\CMS\Model\Site
	 */
// 	function getParentSite() {
// 		return ($this->parent_site) ? CMS::$repository->getEntity('/'.$this->parent_site, false) : null;
// 	}

}
/*
Entity::define_field(__NAMESPACE__.'\Site',
	array(
		'title' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'length' => 50,
			'index' => true,
			'order' => 10
		),
		'domain' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'length' => 20,
			'index' => true,
			'order' => 11
		),
		'parent_site' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => function($entity) {
				return ($entity->path == '/META-SITE') ? 'output' : 'select';
			},
			'options' => function($entity) {
				$options = array('' => Messages::get(__NAMESPACE__.'\Site\parent_site_empty'));
				foreach (CMS::$repository->getChildren('') as $site) {
					$options[$site->getName()] = $site->getName();
				}
				return $options;
			},
			'length' => 20,
			'index' => true,
			'order' => 12
		),
		'hosts' => array(
			'type' => 'array:string',
			'important' => true,
			'field-type' => 'input',
			'order' => 13
		),
		'editor_blockformats' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'order' => 14
		),
		'editor_styles' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'order' => 15
		)
	)
);
*/
