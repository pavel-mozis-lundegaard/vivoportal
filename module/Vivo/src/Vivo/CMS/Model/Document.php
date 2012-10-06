<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Document.php 2136 2012-08-13 14:59:28Z mhajek $
 */
namespace Vivo\CMS\Model;

use Vivo\CMS;
use Vivo\CMS\Workflow;
use Vivo\Context;
use Vivo\CMS\Solr\Indexer;

/**
 * The document represents a folder in tree. The document contains functions for working with content and sub-documents.
 * @author tzajicek
 */
class Document extends Folder {

	const ALL       = 0;
	const PUBLISHED = 1;
	const AVAILABLE = 2;

// 	static $DEFAULT_URL;
// 	static $DEFAULT_URL_PRECEDENCE = false;
// 	static $DEFAULT_HEADING;
// 	static $DEFAULT_OVERVIEW_TITLE;
// 	static $DEFAULT_KEYWORDS;
// 	static $DEFAULT_LAYOUT;
// 	static $DEFAULT_LAYOUT_PANELS = array();
// 	static $DEFAULT_POSITION = 0;
// 	static $DEFAULT_NAVIGABLE = true;
// 	static $DEFAULT_AUTO_VERSIONING = false;
// 	static $DEFAULT_SORTING = '';
// 	static $DEFAULT_SECURED = false;
// 	static $DEFAULT_EXPIRATION = 0;
// 	static $DEFAULT_WORKFLOW = 'Vivo\CMS\Workflow\Basic';
// 	static $DEFAULT_IMAGE;
// 	static $DEFAULT_PUBLISHED;
// 	static $DEFAULT_AUTHOR;

	/**
	 * URL of the document. If there is not specifically specified, the URL is always a straight pathto a document
	 * (relative to the root document ROOT).
	 * @var string
	 */
	private $url; // specificka URL dokumentu

	/**
	 * @var bool URL takes precedence.
	 *
	 * Specificka URL ma prednost (je primarni)
	 */
// 	public $url_precedence; // specificka URL ma prednost (je primarni)
	private $urlPrecedence;

	/**
	 * @var string Page header.
	 */
	private $heading;

	/**
	 * @var string Name in listings
	 */
// 	public $overview_title;
	private $overviewTitle;

	/**
	 * Keywords are used to describe content of the document. Keywords could make fulltext searches faster and more effective.
	 * Please determine each word by comma.
	 * @var string
	 */
	private $keywords;

	/**
	 * Is used to display the contents of the document as a page, when you enter the URL on the front-end.
	 * Settings (assignment) of the layout makes sense only for documents whose content is displayed in HTML format.
	 * @var string
	 * @example Layouts/page/subpage
	 */
	private $layout; // layout (cesta k dokumentu Layoutu)

	/**
	 * @var array Panels in layout.
	 */
	private $layoutPanels;
	/**
	 * If this property is set, the document will appear in the lists of sub-documents (subpages)
	 * on the front-end (overviews, sitemaps, menu, navigation, etc.)
	 * @var bool
	 */
	private $navigable;
	/**
	 * @var bool If this property is set, changes in the contents of the document is automatically saved as a new version of the content.
	 */
	private $autoVersioning = false;
	/**
	 * @var bool Secured (HTTPS required)
	 */
	private $secured;
	/**
	 * Attributes for link tag (A).
	 * @var array
	 */
	private $linkAttributes = array();
	/**
	 * @var array Use document as vocabulary term in Vocalbulary content types
	 */
	private $vocabularies;
	/**
	 * Expiration of the contents of the document - if set, the output display of the contents of the document
	 * is saved to cache and will be displayed within the expiration period from there.
	 * It allows you to accelerate the display of documents containing programming which are time-consuming for processing
	 * (eg, presenting data from an external database).
	 * @var int Expiration (in seconds)
	 */
	private $expiration;
	/**
	 * @var string Forkflow class full name.
	 * @example Vivo\CMS\Workflow\Basic
	 */
	private $workflow;
	/**
	 * Resource image name.
	 * Image could be shown for instance in document listings if template of the listings supports it.
	 * @var string
	 * @example image.jpg
	 */
	private $image;
	/**
	 * Date and time the document was actually published. Typically it is used for articles, newsletters and press releases.
	 * Unless explicitly specified otherwise, the system fills in the date of the creation of the document in the system.
	 * @var DateTime
	 */
	private $published; // logicke datum vydani (nezamenovat s publikaci obsahu)
	/**
	 * Name of the person who actually created the document. It is used typically for articles, newsletters and press releases.
	 * Unless explicitly specified otherwise, the system fills in a name of the logged editor.
	 * @var string
	 */
	private $author;
	/**
	 * Internal publising notices
	 * @var string
	 */
	private $internalNotice;

	/**
	 * @param string $path Repository path.
	 * @param Vivo\CMS\Model\Entity\Security $security
	 */
	function __construct($path = null, $security = null) {
		parent::__construct($path, $security);

// 		if(self::$DEFAULT_POSITION != 0) {
// 			CMS::$logger->warn('Property $DEFAULT_POSITION in model Document is deprecated.');
// 		}
// 		if(self::$DEFAULT_SORTING != '') {
// 			CMS::$logger->warn('Property $DEFAULT_SORTING in model Document is deprecated.');
// 		}

// 		$this->url = self::$DEFAULT_URL;
// 		$this->url_precedence = self::$DEFAULT_URL_PRECEDENCE;
// 		$this->heading = self::$DEFAULT_HEADING;
// 		$this->overview_title = self::$DEFAULT_OVERVIEW_TITLE;
// 		$this->keywords = self::$DEFAULT_KEYWORDS;
// 		if (!$this->layout)
// 			$this->layout = self::$DEFAULT_LAYOUT;
// 		$this->layout_panels = self::$DEFAULT_LAYOUT_PANELS;
// 		$this->navigable = self::$DEFAULT_NAVIGABLE;
// 		$this->auto_versioning = self::$DEFAULT_AUTO_VERSIONING;
// 		$this->secured = self::$DEFAULT_SECURED;
// 		$this->expiration = self::$DEFAULT_EXPIRATION;
// 		$this->workflow = self::$DEFAULT_WORKFLOW;
// 		$this->image = self::$DEFAULT_IMAGE;
// 		$this->published = self::$DEFAULT_PUBLISHED ? : new \DateTime;
// 		$this->author = self::$DEFAULT_AUTHOR;
	}

	/**
	 * Page header. If heading is not set, default document name will be returned.
	 * @return string
	 */
	public function getHeading() {
		return $this->heading ? $this->heading : $this->title;
	}

	/**
	 * Document overview title. If overview title is not set, document title will be returned.
	 * @return string
	 */
	public function getOverviewTitle() {
		return $this->overviewTitle ? $this->overviewTitle : $this->title;
	}

	/**
	 * Returns image URL or NULL.
	 * @return string|null
	 */
// 	public function getImageURL() {
// 		return $this->image ? $this->getURL().$this->image : null;
// 	}

	/**
	 * Create new content by class name.
	 * @param string $class_name
	 * @return object
	 */
// 	function createContent($class_name) {
// 		return new $class_name;
// 	}

	/**
	 * Returns contents.
	 * @param int $index Content index means position in the MultiContentDocument.
	 * @return array
	 */
// 	function getContents($index = false) {
// 		return CMS::$repository->getChildren($this->path.'/Contents'.(($index && $index > 1) ? '.'.$index : ''));
// 	}

	/**
	 * Returns content by content version.
	 *
	 * @todo Exception misto null?
	 *
	 * @param int $version Content version.
	 * @param int $index Content index means position in the Multi Content Document.
	 * @return Vivo\CMS\Model\Content|null
	 */
	public function getContent($version, $index = false) {
		$contents = $this->getContents($index);
		/**
		 * Pokud nastane neintegrita rady (verze 0,1,3), zde nastava problem - indexy pole jsou ale po sobe jdouci klice.
		 * FIX je zatim odlozen, dokud se neprijde na pricinu.
		 * Staci ze porovnovat $version != $contents[$version]->getVersion()
		 */
		return array_key_exists($version, $contents) ? $contents[$version] : null;
	}

	/**
	 * Returns count of contents in the MultiContentDocument
	 * @return int
	 */
	public function getContentCount() {
		for ($index = 1; $index <= 20; $index++)
			if (!$this->getContents($index))
				break;
		return $index - 1;
	}

	/**
	 * Returns sub-documents of this Document.
	 * @param string $class_name
	 * @param int $deep
	 * @param int $which
	 * @return array
	 */
	public function getChildren($class_name = false, $deep = false, $which = self::ALL) {
		if (!$class_name)
			$class_name = 'Vivo\CMS\Model\Folder';
		$children = parent::getChildren($class_name, $deep); //TODO which support (use rather querying)
		if ($which == self::AVAILABLE) {
			$available_children = array();
			foreach ($children as $child)
				if (!\Vivo\Util\Object::is_a($child, 'Vivo\CMS\Model\Document') || ($child->navigable && $child->isPublished()))
					$available_children[] = $child;
			return $available_children;
		}
		else if($which == self::PUBLISHED) {
			$available_children = array();
			foreach ($children as $child)
				if (!\Vivo\Util\Object::is_a($child, 'Vivo\CMS\Model\Document') || $child->isPublished())
					$available_children[] = $child;
			return $available_children;
		} else {
			return $children;
		}
	}

	/**
	 * Returns the document which sub-documents of it should be displayed in the menu or overview.
	 * @return array
	 */
	public function getAvailableChildren() {
		return $this->getChildren('Vivo\CMS\Model\Document', false, self::AVAILABLE);
	}

	/**
	 * Returns workflow.
	 * @return string
	 */
	public function getWorkflow() {
		return $this->workflow;
	}

	/**
	 * @param bool $throw_exception
	 * @param int $index Content index means position in the MultiContentDocument.
	 * @throws Vivo\CMS\NoPublishedContentException
	 * @return Vivo\CMS\Model\Content|null
	 */
	public function getPublishedContent($throw_exception = true, $index = false) {
		return $this->getWorkflow()->getPublishedContent($this, $throw_exception, $index);
	}

	/**
	 * Returns all published contents of this Document / MultiContentDocument
	 * @return array
	 */
	public function getPublishedContents() {
		$contents = array();
		for ($index = 1; $index <= $this->getContentCount(); $index++)
			if ($content = $this->getPublishedContent(false, $index))
				$contents[$index] = $content;
		return $contents;
	}

	/**
	 * Returns the last content. If this Document is type of MultiContentDocument, returns last content from first position / index.
	 * @param bool $throw_exception
	 * @throws Vivo\CMS\NoContentException
	 * @return Vivo\CMS\Model\Content|null
	 */
	public function getLastContent($throw_exception = true) {
		$contents = $this->getContents();
		$index = ($count = count($contents)) == 0 ? 0 : $count - 1;
		$content = isset($contents[$index]) ? $contents[$index] : null;
		if (!$content && $throw_exception)
			throw new CMS\NoContentException($this->path);
		return $content;
	}

	/**
	 * If this Document has at least one published content, returns true otherwise false.
	 * @return bool
	 */
	public function isPublished() {
		return $this->getPublishedContent(false) ? true : false;
	}

	/**
	 * Icon name
	 * @return string
	 */
// 	public function getIcon() {
// 		if (!($content = $this->getPublishedContent(false)))
// 			$content = $this->getLastContent(false);
// 		return $content ? $content->getIcon() : 'Document';
// 	}

	/**
	 * Returns a key chain for indexer. Through this chain, the document sought.
	 * @param array $field_names Field names will be indexed.
	 * @return string
	 */
	public function getTextContent($field_names = array()) {
		return parent::getTextContent(array_merge($field_names, array('title', 'keywords')));
	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_published_asc($doc1, $doc2) {
		return $doc1->published >= $doc2->published ?
			$doc1->published == $doc2->published ? 0 : 1 : -1;
	}

	/**
	 * Comparation function.
	 * @param Vivo\CMS\Model\Document $doc1
	 * @param Vivo\CMS\Model\Document $doc2
	 * @return int
	 */
	function cmp_published_desc($doc1, $doc2) {
		return $doc1->published <= $doc2->published ?
			$doc1->published == $doc2->published ? 0 : 1 : -1;
	}
	/*
	 * trideni cmp funkcemi se realne vyuziva jenom na back-endu (front-end pouziva prehledy vyuzivajici fulltext)
	 * a neni ucelne uzivateli v back-endu neustale menit poradi childu, proto zakomentovano
	function cmp_RANDOM($doc1, $doc2) {
		return rand(0, 2) - 1;
	}
	*/
	/**
	 * @var array Transfer array between the old and new type of record.
	 */
	private $old_new_sorting = array(
		'created+'		=> 'vivo_cms_model_entity_created asc',
		'created-'		=> 'vivo_cms_model_entity_created desc',
		'title+'		=> 'vivo_cms_model_folder_title asc',
		'title-'		=> 'vivo_cms_model_folder_title desc',
		'position+'		=> 'vivo_cms_model_folder_position asc',
		'position-'		=> 'vivo_cms_model_folder_position desc',
		'published+'	=> 'vivo_cms_model_document_published asc',
		'published-'	=> 'vivo_cms_model_document_published desc',
	);

	/**
	 * Returns sorting string.
	 * @return string
	 */
	function sorting() {
		return is_string($this->sorting) && isset($this->old_new_sorting[$this->sorting])
					? $this->old_new_sorting[$this->sorting]
					: $this->sorting;
	}

	/**
	 * Returns class names of contents for Multi Content Document
	 * <code>
	 * 	array(
	 * 		1 =>'Vivo\CMS\Model\Content\File:text/html',
	 * 			'Vivo\CMS\Model\Content\File:text/html',
	 *			'Vivo\CMS\Model\Content\Gallery',
	 *			'Vivo\CMS\Model\Content\Discussion',
	 *			'Vivo\CMS\Model\Content\Component:MyProject\CMS\UI\Content\MyComponent
	 *		);
	 * </code>
	 * @return null|array
	 */
	public function getMultiContentTypes() {
		return null; // nespecificky pocet nespecifickych typu obsahu
	}

	public function getType() {
		return get_class($this);
	}
}
/*
Entity::define_field(__NAMESPACE__.'\Document',
	array(
		'allow_listing' => array(
			'field-type' => false,
		),
		'url' => array(
			'type' => 'string',
			'field-type' => 'input',
			'length' => 100,
			'index' => true,
			'order' => 8
		),
		'url_precedence' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'order' => 9
		),
		'link_attributes' => array(
			'type' => 'hashtable:string',
			'field-type' => 'input',
			'length' => 100,
			'order' => 10
		),
		'heading' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'input',
			'length' => 100,
			'order' => 11
		),
		'overview_title' => array(
			'type' => 'string',
			'important' => true,
			'field-type' => 'textarea',
			'field-attributes' => array('rows' => 2, 'cols' => 20),
			'length' => 100,
			'order' => 12
		),
		'keywords' => array(
			'type' => 'string',
			'field-type' => 'input',
			'important' => true,
			'length' => 100,
			'index' => true,
			'order' => 14
		),
		'layout_panels' => array(
			'field-type' => 'input',
			'field-class' => 'Vivo\CMS\UI\Form\Field\PanelSelect',
			'field-attributes' => array('rows' => 5, 'cols' => 20),
			'order' => 15
		),
		'image' => array(
			'type' => 'string',
			'field-type' => function($entity) {
				return isset($entity->created) ? 'input' : false;
			},
			'field-class' => 'Vivo\CMS\UI\Form\Field\EntityResource',
			'field-properties' => array(
				'name' => 'image'
			),
			'important' => true,
			'order' => 17
		),
		'layout' => array(
			'type' => 'string',
			'field-type' => 'input',
			'field-class' => 'Vivo\CMS\UI\Form\EntitySelect',
			'field-properties' => array(
				'base_path' => 'ROOT/Layouts'
			),
			'index' => true,
			'order' => 18
		),
		'navigable' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'index' => true,
			'order' => 20
		),
		'searchable' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'order' => 21
		),
		'auto_versioning' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'index' => true,
			'order' => 22
		),
		'secured' => array(
			'type' => 'boolean',
			'field-type' => 'input',
			'field-attributes' => array(
				'type' => 'checkbox',
				'value' => 1
			),
			'index' => true,
			'order' => 24
		),
		'expiration' => array(
			'type' => 'integer',
			'field-type' => 'input',
			'index' => true,
			'order' => 25
		),
		'analytic_code' => array(
			'type' => 'string',
			'field-type' => 'input',
			'order' => 26
		),
		'vocabularies' => empty(Context::$instance->site->content_types['Vivo\CMS\Model\Content\Vocabulary']) ?
		    array(
                'type' => 'string'
            ) : array(
			'type' => 'string',
			'index' => true,
			'order' => 27,
			'field-type' => 'select',
			'field-class' => 'Vivo\CMS\UI\Form\Field\MultipleSelect',
			'field-properties' => array('mandatory' => true),
			'field-attributes' => array('multiple' => 'multiple', 'size' => 5, 'sortable' => false),
			'options' => function($entity) {
				$options = array();
				$contents = CMS::$repository->indexer->query('vivo_cms_model_entity_path:'.Indexer::escape(Context::$instance->site->path).'/ROOT/* AND vivo_cms_model_entity_type:'.Indexer::escape('Vivo\CMS\Model\Content\Vocabulary').' AND vivo_cms_model_entity_published:1', array('limit' => Indexer::MAX_ROWS));
				foreach ($contents as $content) {
					$doc = $content->getDocument();
					$options[$doc->uuid]['label'] = htmlspecialchars($doc->title);
					$options[$doc->uuid]['title'] = '[ref:'.$doc->uuid.']';
				}
				return $options;
		     }
		),
		'published' => array(
			'type' => 'DateTime',
			'field-type' => 'input',
			'field-attributes' => array(
				'class' => 'datetime'
			),
			'index' => true,
			'important' => true,
			'order' => 1001
		),
		'author' => array(
			'type' => 'string',
			'field-type' => 'input',
			'index' => true,
			'important' => true,
			'order' => 1003
		),
		'internal_notice' => array(
			'type' => 'string',
			'field-type' => 'textarea',
			'field-attributes' => array('rows'=>5, 'cols'=>20),
			'index' => false,
			'important' => false,
			'order' => 100000
		)
	)
);
*/
