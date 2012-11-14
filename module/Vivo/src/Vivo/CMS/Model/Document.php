<?php
namespace Vivo\CMS\Model;

use Vivo\CMS;
use Vivo\CMS\Workflow;
use Vivo\Context;
use Vivo\CMS\Solr\Indexer;

/**
 * The document represents a folder in tree. The document contains functions for working with content and sub-documents.
 */
class Document extends Folder {

	const ALL       = 0;
	const PUBLISHED = 1;
	const AVAILABLE = 2;

	/**
	 * URL of the document. If there is not specifically specified, the URL is always a straight pathto a document
	 * (relative to the root document ROOT).
	 * @var string
	 */
	protected $url; // specificka URL dokumentu
	/**
	 * @var bool URL takes precedence.
	 *
	 * Specificka URL ma prednost (je primarni)
	 */
	protected $urlPrecedence;
	/**
	 * @var string Page header.
	 */
	protected $heading;
	/**
	 * @var string Name in listings
	 */
	protected $overviewTitle;
	/**
	 * Keywords are used to describe content of the document. Keywords could make fulltext searches faster and more effective.
	 * Please determine each word by comma.
	 * @var string
	 */
	protected $keywords;
	/**
	 * Is used to display the contents of the document as a page, when you enter the URL on the front-end.
	 * Settings (assignment) of the layout makes sense only for documents whose content is displayed in HTML format.
	 * @var string
	 * @example Layouts/page/subpage
	 */
	protected $layout; // layout (cesta k dokumentu Layoutu)
	/**
	 * @var array Panels in layout.
	 */
	protected $layoutPanels = array();
	/**
	 * If this property is set, the document will appear in the lists of sub-documents (subpages)
	 * on the front-end (overviews, sitemaps, menu, navigation, etc.)
	 * @var bool
	 */
	protected $navigable;
	/**
	 * @var bool If this property is set, changes in the contents of the document is automatically saved as a new version of the content.
	 */
	protected $autoVersioning = false;
	/**
	 * @var bool Secured (HTTPS required)
	 */
	protected $secured;
	/**
	 * Attributes for link tag (A).
	 * @var array
	 */
	protected $linkAttributes = array();
	/**
	 * @var array Use document as vocabulary term in Vocalbulary content types
	 */
	protected $vocabularies;
	/**
	 * Expiration of the contents of the document - if set, the output display of the contents of the document
	 * is saved to cache and will be displayed within the expiration period from there.
	 * It allows you to accelerate the display of documents containing programming which are time-consuming for processing
	 * (eg, presenting data from an external database).
	 * @var int Expiration (in seconds)
	 */
	protected $expiration;
	/**
	 * @var string Forkflow class full name.
	 * @example Vivo\CMS\Workflow\Basic
	 */
	protected $workflow;
	/**
	 * Resource image name.
	 * Image could be shown for instance in document listings if template of the listings supports it.
	 * @var string
	 * @example image.jpg
	 */
	protected $image;
	/**
	 * Date and time the document was actually published. Typically it is used for articles, newsletters and press releases.
	 * Unless explicitly specified otherwise, the system fills in the date of the creation of the document in the system.
	 * @var DateTime
	 */
	protected $published; // logicke datum vydani (nezamenovat s publikaci obsahu)
	/**
	 * Name of the person who actually created the document. It is used typically for articles, newsletters and press releases.
	 * Unless explicitly specified otherwise, the system fills in a name of the logged editor.
	 * @var string
	 */
	protected $author;
	/**
	 * Internal publising notices
	 * @var string
	 */
	protected $internalNotice;

	/**
	 * @param string $path Repository path.
	 * @param Vivo\CMS\Model\Entity\Security $security
	 */
	public function __construct($path = null, $security = null) {
		parent::__construct($path, $security);
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

	public function setWorkflow($workflow) {
		$this->workflow = $workflow;
	}

	/**
	 * Returns workflow.
	 * @return string
	 */
	public function getWorkflow() {
		return $this->workflow;
	}

	/**
	 * Returns a key chain for indexer. Through this chain, the document sought.
	 * @param array $field_names Field names will be indexed.
	 * @return string
	 */
	public function getTextContent($field_names = array()) {
		return parent::getTextContent(array_merge($field_names, array('title', 'keywords')));
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

	/**
	 * Returns path of layout document.
	 * @return string
	 */
	public function getLayout()
	{
        return $this->layout;
	}

	public function getLayoutPanels()
	{
	     return $this->layoutPanels;
	}
}
