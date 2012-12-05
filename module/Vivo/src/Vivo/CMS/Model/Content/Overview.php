<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * VIVO model represents overview documents by path and other criteria on front-end.
 */
class Overview extends Model\Content {

	const TYPE_DYNAMIC = 'DYNAMIC';
	const TYPE_STATIC = 'STATIC';

	/**
	 * @var string View / tempate path.
	 */
	protected $frontView;

	//@todo: proc se tohle vse jmenuje overviewXXXX - proc to neni rovnou $this->XXXX ????????????????
	// +1 grafa

	/**
	 * Overview type.
	 *
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
	 *
	 * @param string $path Entity path
	 */
	public function __construct($path = null) {
		parent::__construct($path);
	}

	/**
	 * Sets overview type
	 *
	 * @param string $type Overview type 
	 **/
	public function setType($type) {
		$this->overviewType = $type;
	}

	/**
	 * @param array $field_names
	 * @return string
	 */
	public function getTextContent($field_names = array()) {
		return parent::getTextContent(array_merge($field_names, array('overview_path', 'overview_items')));
	}

	public function getOverviewPath() {
	    return $this->overviewPath;
	}

	public function getOverviewItems() {
	    return $this->overviewItems;
	}

	public function getOverviewType() {
	    return $this->overviewType;
	}

}
