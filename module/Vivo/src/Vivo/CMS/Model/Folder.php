<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Folder.php 2267 2012-09-10 08:11:40Z palbrecht $
 */
namespace Vivo\CMS\Model;

use Vivo\CMS;
use Vivo\CMS\Security;
use Vivo\CMS\Solr\Indexer;

/**
 * Represents folder in tree.
 * @see Vivo\CMS\Model\Document
 * @author tzajicek
 */
class Folder extends Entity {

// 	static $DEFAULT_TITLE;
// 	static $DEFAULT_LANGUAGE = 'cs';
// 	static $DEFAULT_DESCRIPTION;
// 	static $DEFAULT_ALLOW_LISTING = false;
// 	static $DEFAULT_POSITION = 0;
// 	static $DEFAULT_SORTING = '';
// 	static $DEFAULT_SECURITY;

	/**
	 * @var string Folder name.
	 */
	protected $title;
	/**
	 * @var string Language.
	 */
	protected $language;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var bool
	 */
// 	public $allow_listing;
	protected $allowListing;
	/**
	 * @var int Position of the document in layer. This property could be used as sorting option of the document.
	 * @see self::$sorting
	 */
	protected $position;
	/**
	 * @var string Specifies which criteria will classify sub-documents in the lists (newsletters, sitemap, menu, etc.)
	 * @see $FIELDS
	 * @example vivo_cms_model_document_position asc
	 */
	protected $sorting;
	/**
	 * @var string Replication id;
	 */
	protected $replicationGroupId;
	/**
	 * Absolute last path to entity stored in repository before move to trash.
	 * @var string
	 */
	protected $lastPath;
	/**
	 * @var Vivo\CMS\Model\Entity\Security
	 */
	protected $security;

	/**
	 * @param string $path Folder (entity) path in CMS repository.
	 * @param Vivo\CMS\Model\Entity\Security $security
	 */
	function __construct(/*$path = null, */$security = null) {
		parent::__construct($path);
// 		$this->title = self::$DEFAULT_TITLE;
// 		$this->language = self::$DEFAULT_LANGUAGE;
// 		$this->description = self::$DEFAULT_DESCRIPTION;
// 		$this->allow_listing = self::$DEFAULT_ALLOW_LISTING;
// 		$this->position = self::$DEFAULT_POSITION;
// 		$this->sorting = self::$DEFAULT_SORTING;
		$this->security =
			$security ? :
				(self::$DEFAULT_SECURITY ? :
					new CMS\Model\Entity\Security(
						array(
							Security\Manager::ROLE_VISITOR			=> array(Security\Manager::GROUP_ANYONE),
							Security\Manager::ROLE_PUBLISHER		=> array(Security\Manager::GROUP_PUBLISHERS),
							Security\Manager::ROLE_ADMINISTRATOR	=> array(Security\Manager::GROUP_ADMINISTRATORS)
						)
					));
	}

	/**
	 * @deprecated
	 */
// 	function getSiteName() {
// 		return substr($this->path, 1, strpos($this->path, '/', 1) - 1);
// 	}

	/**
	 * @deprecated
	 */
// 	function getSite() {
// 		return CMS::$repository->getEntity('/'.$this->getSiteName());
// 	}

	/**
	 * @param string $type	Model class name. Default: Vivo\CMS\Model\Folder
	 * @return array
	 */
// 	function getParents($type = 'Vivo\CMS\Model\Folder') {
// 		$parents = array();
// 		$folder = $this;
// 		while (($parent = $folder->getParent()) && \Vivo\Util\Object::is_a($parent, $type)) {
// 			array_unshift($parents, $folder = $parent);
// 		}
// 		return $parents;
// 	}

	/**
	 * @return string
	 */
	public function getIcon() {
		$icon = 'Folder';
		if (in_array($this->getName(), array('Components', 'Layouts', 'Files', 'Trash')))
			$icon.= '.'.$this->getName();
		return $icon;
	}

	/**
	 * @param array $field_names
	 * @return string
	 */
	function getTextContent($field_names = array()) {
		return parent::getTextContent(array_merge($field_names, array('description')));
	}

	/**
	 * Comparation function.
	 * @see strcoll()
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_title_asc($doc1, $doc2) {
		return strcoll($doc1->title, $doc2->title);
	}

	/**
	 * Comparation function.
	 * @see strcoll()
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_title_desc($doc1, $doc2) {
		return strcoll($doc2->title, $doc1->title);
	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_position_asc($doc1, $doc2) {
		return $doc1->position - $doc2->position;
	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_position_desc($doc1, $doc2) {
		return $doc2->position - $doc1->position;
	}

}
/*
Entity::define_field(__NAMESPACE__.'\Folder',
	array(
		'title' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'field-attributes' => array(
				'onkeyup' => 'if (this.form.entity_new.value == 1) this.form.entity_name.value = updatePath(this.form.entity_name.value, this.value)',
				'onchange' => 'this.onkeyup()'
			),
			'field-validators' => array(
				'notEmpty' => array(
					'Vivo\CMS\UI\Form\Field\must_be_filled', null
				)
			),
			'length' => 100,
			'index' => true,
			'order' => 6
		),
		'name' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'length' => 100,
			'order' => 7,
			'disabled' => function($entity) {
				return isset($entity->created);
			}
		),
		'description' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'textarea',
			'field-attributes' => array('rows' => 5, 'cols' => 20),
			'length' => 250,
			'index' => true,
			'order' => 13
		),
		'language' => array(
			'type' => 'string',
			'field-type' => 'select',
			'options' => function($document) {
				return CMS::$parameters['languages'];
			},
			'length' => 2,
			'index' => true,
			'order' => 15
		),
		'position' => array(
			'type' => 'integer',
			'field-type' => 'input',
			'index' => true,
			'order' => 16
		),
		'allow_listing' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'index' => true,
			'order' => 19
		),
		'sorting' => array(
			'type' => 'string',
			'field-type' => 'select',
			'options' => array(
// 				''											=> __NAMESPACE__.'\Folder\sorting_none',
				'vivo_cms_model_folder_title asc'			=> __NAMESPACE__.'\Folder\sorting_title',
				'vivo_cms_model_folder_title desc'			=> __NAMESPACE__.'\Folder\sorting_title_desc',
				'vivo_cms_model_entity_created asc'			=> __NAMESPACE__.'\Folder\sorting_created',
				'vivo_cms_model_entity_created desc'		=> __NAMESPACE__.'\Folder\sorting_created_desc',
				'vivo_cms_model_entity_modified asc'		=> __NAMESPACE__.'\Folder\sorting_modified',
				'vivo_cms_model_entity_modified desc'		=> __NAMESPACE__.'\Folder\sorting_modified_desc',
				'vivo_cms_model_folder_position asc'		=> __NAMESPACE__.'\Folder\sorting_position',
				'vivo_cms_model_folder_position desc'		=> __NAMESPACE__.'\Folder\sorting_position_desc',
				'vivo_cms_model_document_published asc'		=> __NAMESPACE__.'\Document\sorting_published',
				'vivo_cms_model_document_published desc'	=> __NAMESPACE__.'\Document\sorting_published_desc',
				Indexer::SORT_RANDOM						=> __NAMESPACE__.'\Folder\sorting_random',
			),
			'length' => 20,
			'index' => true,
			'order' => 23
		),
		'replicationGroupId' => array(
			'type' => 'string',
			'field-type' => 'input',
			'length' => 35,
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => true,
			'order' => 99
		),
		'lastPath' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-attributes' => array(
				'readonly' => 'readonly'
			),
			'index' => false,
			'order' => 100
		)
	)
);
*/

