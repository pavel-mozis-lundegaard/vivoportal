<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * Model Layout represents page container. Layout carries information about the appearance of the page. Defines the layout of the components and their interdependence.
 */
class Layout extends Model\Content {

	/**
	 * @var array of paths of documents for layout panels
	 */
	private $panels = array();

	/**
	 * Setting default values
	 * @param string $path Entity path
	 */
	public function __construct($path = null) {
		parent::__construct($path);
	}

	public function getLayoutPanels() {
		return $this->panels;
	}
}
