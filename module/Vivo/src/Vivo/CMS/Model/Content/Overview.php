<?php
namespace Vivo\CMS\Model\Content;

// use Vivo;
// use Vivo\Context;
use Vivo\CMS\Model;
// use Vivo\CMS\Solr\Indexer;

/**
 * VIVO model represents overview documents by path and other criteria on front-end.
 * @author tzajicek, zstanek
 */
class Overview extends Model\Content {

	const FRONT_COMPONENT = 'Vivo\CMS\UI\Content\Overview';
	const EDITOR_COMPONENT = 'Vivo\CMS\UI\Content\Editor\Overview';
	const TYPE_DYNAMIC = 'DYNAMIC';
	const TYPE_STATIC = 'STATIC';

// 	static $DEFAULT_FRONT_VIEW = false;
// 	static $DEFAULT_PATH;
// 	static $DEFAULT_CRITERIA = '';
// 	static $DEFAULT_SORTING;
// 	static $DEFAULT_LIMIT = 1000;
// 	static $FRONT_VIEW_EXPANDABLE = 'Vivo/CMS/UI/Content/Overview.Expandable';
// 	static $FRONT_VIEW_DESCRIPTION = 'Vivo/CMS/UI/Content/Overview.Description';
// 	static $FRONT_VIEW_CAROUSEL = 'Vivo/CMS/UI/Content/Overview.Carousel';
// 	static $FRONT_VIEW_FILEBOARD = 'Vivo/CMS/UI/Content/Overview.Fileboard';
// 	static $FRONT_VIEW_THUMBDESC = 'Vivo/CMS/UI/Content/Overview.ThumbDesc';
// 	static $FRONT_VIEW_THUMBNAILS = 'Vivo/CMS/UI/Content/Overview.Thumbnails';
// 	static $FRONT_VIEW_WITHDATE = 'Vivo/CMS/UI/Content/Overview.WithDate';
// 	static $FRONT_VIEW_WITHDATEDESC = 'Vivo/CMS/UI/Content/Overview.WithDateDesc';
// 	static $FRONT_VIEW_WITHDATEDESCTHUMB = 'Vivo/CMS/UI/Content/Overview.WithDateDescThumb';

	/**
	 * @var string View / tempate path.
	 * <code>
	 * // How to add new template for a overview in init.php
	 * Vivo::load('Vivo\CMS\Model\Content\Overview');
	 * Vivo\CMS\Model\Entity::$FIELDS['Vivo\CMS\Model\Content\Overview']['front_view']['options']['Vivo/CMS/UI/Content/Overview.Menu'] = 'Main menu';
	 * Vivo\CMS\Model\Entity::$FIELDS['Vivo\CMS\Model\Content\Overview']['front_view']['options']['Vivo/CMS/UI/Content/Overview.SiteMap'] = 'Site map';
	 * </code>
	 */
	protected $frontView;


	//@todo: proc se tohle vse jmenuje overviewXXXX - proc to neni rovnou $this->XXXX ????????????????

	/**
	 * Overview type.
	 * @var string see TYPE_DYNAMIC and TYPE_STATIC constants
	 */
	protected $overviewType;

	/**
	 * @var string Path to a document, which sub-documents of it should be displayed in the overview. If a overview path is not set, it shows sub-documents of the current document, which overview is the content of that document.
	 * @example en/news/archive/
	 */
	protected $overviewPath;

	/**
	 * @var string Fulltext criteria.
	 */
	protected $overviewCriteria;

	/**
	 * @var string Documents sorting.
	 * @see Vivo\CMS\Model\Document::$sorting
	 */
	protected $overviewSorting;

	/**
	 * @var int A number represent documents count in overview.
	 */
	protected $overviewLimit;

	/**
	 * @var array items for static overview.
	 */
	protected $overviewItems = array();

	/**
	 * Setting default values
	 * @param string $path Entity path
	 */
	public function __construct($path = null) {
		parent::__construct($path);
// 		$this->front_view = self::$DEFAULT_FRONT_VIEW;
// 		$this->overview_path = self::$DEFAULT_PATH;
// 		$this->overview_criteria = self::$DEFAULT_CRITERIA;
// 		$this->overview_sorting = self::$DEFAULT_SORTING;
// 		$this->overview_limit = self::$DEFAULT_LIMIT;
	}

	public function setType($type) {
		$this->overviewType = $type;
	}

	/**
	 * @todo: Do Nejakyho Helperu, tady fakt ne
	 *
	 * Icon name
	 * @return string
	 */
// 	public function getIcon() {
// 		return 'Overview';
// 	}

	/**
	 * @todo do Business COMPONENTY
	 *
	 * Returns the document which sub-documents of it should be displayed in the overview.
	 * @see self::$overview_path
	 * @return Vivo\CMS\Model\Document
	 */
// 	public function getOverviewDocument() {
// 		$document = $this->getDocument();
// 		if ($overview_path = $this->overview_path) {
// 			// podpora vsech moznych formatu cesty: a) ROOT/(...), b) /Site/ROOT(/...), c) ... (doporuceny)
// 			if (strpos($overview_path, 'ROOT/') === 0)
// 				$overview_path = '/'.$document->getSiteName().'/'.$overview_path;
// 			elseif (strpos($this->overview_path, '/ROOT'));
// 			else
// 				$overview_path = '/'.$document->getSiteName().'/ROOT/'.trim($overview_path, '/');

// 			$overview_path = str_replace('{language}', Context::$instance->document->language, $overview_path);
// 			return Context::$instance->site->getEntity($overview_path);
// 		} else {
// 			return $document;
// 		}
// 	}

	/**
	 * @param array $field_names
	 * @return string
	 */
	public function getTextContent($field_names = array()) {
		return parent::getTextContent(array_merge($field_names, array('overview_path', 'overview_items')));
	}

	/**
	 * Returns query for SOLR.
	 * If overview document is a Folder, sorting by position asc will be set.
	 * @param string $query
	 * @param string $sorting
	 * @param int $limit
	 */
// 	public function indexer_parameters(&$query, &$sorting, &$limit) {
// 		$overview_document = $this->getOverviewDocument();
// 		$query =
// 			preg_replace_callback('/\${([^}]+)}/',
// 				function ($matches) {
// 					if ($pos = strpos($matches[1], ':'))
// 						list($name, $default_value) = explode(':', $matches[1]); else
// 						$name = $matches[1];
// 					return ($value = Context::$instance->parameters[$name]) ? $value : $default_value;
// 				},
// 				'vivo_cms_model_entity_path:'.$overview_document->path.'/* '.
// 					' AND vivo_cms_model_entity_published:1'.
// 					' AND '.Indexer::getEntityTypeQueryCond().
// 					' AND '.(
// 						$this->overview_criteria ?
// 							'('.$this->overview_criteria.')' :
// 							'!vivo_cms_model_entity_path:'.$overview_document->path.'/*/*'
// 					)
// 			);

// 		$sorting =
// 			$this->overview_sorting ?
// 				$this->overview_sorting : (
// 					(($sorting = (get_class($overview_document) == 'Vivo\CMS\Model\Folder') ? 'vivo_cms_model_folder_position asc' : $overview_document->sorting()) && is_string($sorting)) ?
// 						$sorting :
// 						''
// 				);
// 		$limit =
// 			$this->overview_limit ?
// 				$this->overview_limit :
// 				1000;
// 	}

}
/*
Model\Entity::$FIELDS[__NAMESPACE__.'\Overview'] =
	array(
		'front_view' => array(
			'type' => 'string',
			'field-type' => 'select',
			'options' => array(
				'' => __NAMESPACE__.'\Overview\front_view_default',
				Overview::$FRONT_VIEW_DESCRIPTION => __NAMESPACE__.'\Overview\front_view_description',
				Overview::$FRONT_VIEW_WITHDATE => __NAMESPACE__.'\Overview\front_view_withdate',
				Overview::$FRONT_VIEW_WITHDATEDESC => __NAMESPACE__.'\Overview\front_view_withdatedesc',
				Overview::$FRONT_VIEW_THUMBNAILS => __NAMESPACE__.'\Overview\front_view_thumbnails',
				Overview::$FRONT_VIEW_THUMBDESC => __NAMESPACE__.'\Overview\front_view_thumbdesc',
				Overview::$FRONT_VIEW_WITHDATEDESCTHUMB => __NAMESPACE__.'\Overview\front_view_withdatedescthumb',
				Overview::$FRONT_VIEW_FILEBOARD => __NAMESPACE__.'\Overview\front_view_fileboard',
				Overview::$FRONT_VIEW_EXPANDABLE => __NAMESPACE__.'\Overview\front_view_expandable',
				Overview::$FRONT_VIEW_CAROUSEL => __NAMESPACE__.'\Overview\front_view_carousel'
			),
			'important' => true,
			'order' => 100
		)
	);
*/
