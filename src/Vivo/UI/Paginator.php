<?php

namespace Vivo\UI;

use Vivo;

/**
 * Paginator is a componet for pagination.
 */
class Paginator extends Component {

	/**
	 * @var int
	 */
	private $itemCount;

	/**
	 * @var int
	 */
	private $itemsPerPage;

	/**
	 * @var int
	 */
	private $page;

	/**
	 * @var int
	 */
	private $pageCount;

	/**
	 * @var string
	 */
	private $paramName;

	private $request;

	/**
	 * @var array
	 */
	private $params = array();

	public function __construct($request) {
		$this->request = $request;
		$this->itemCount = 0;
		$this->itemsPerPage = 10;
		$this->page = 1;
	}

	public function __set($name, $value) {
		method_exists($this, 'set'.ucfirst($name)) ?
			$this->{'set'.ucfirst($name)}($value) :
			parent::__set($name, $value);
	}

	public function __get($name) {
		return method_exists($this, 'get'.ucfirst($name)) ?
			$this->{'get'.ucfirst($name)}() :
			parent::__get($name);
	}

	public function initSetPage() {
		if ($page = $this->request->getQuery($this->getParamName()))
			$this->setPage($page);
	}

	/**
	 * Returns param name
	 * @return string
	 */
	public function getParamName() {
		if (!$this->paramName)
			$this->paramName = 'page'.dechex(crc32($this->getPath()));
		return $this->paramName;
	}

	/**
	 * Add param. From params will be generated the query string.
	 * @param string $name Param name.
	 * @param string $value Param value.
	 */
	public function addParam($name, $value) {
		$this->params[$name] = $value;
	}

	/**
	 *
	 * @param int $page Page number.
	 * @param bool $htmlescape
	 */
	public function getQueryString($page = null, $htmlescape = true) {
		$q = array();
		$page = max(1, $page === null ? $this->page : (int) $page);
		if ($page !== 1)
			$q[] = urlencode($this->getParamName()).'='.urlencode($page);
		foreach ($this->params as $name=>$value)
			$q[] = urlencode($name).'='.urlencode($value);
		return implode($htmlescape ? '&amp;' : '&', $q);
	}

	/**
	 * First page.
	 * @return bool
	 */
	public function isFirst() {
		return $this->page == $this->firstPage;
	}

	/**
	 * Last page.
	 * @return bool
	 */
	public function isLast() {
		return $this->page == $this->lastPage;
	}

	/**
	 * Sets page counts.
	 */
	protected function recalculate() {
		$this->pageCount = ceil($this->itemCount/$this->itemsPerPage);
	}


	/*** setters ***/

	/**
	 * @param int $itemCount
	 */
	public function setItemCount($itemCount) {
		$this->itemCount = max((int)$itemCount,0);
		$this->recalculate();
	}

	/**
	 * @param int $itemsPerPage
	 */
	public function setItemsPerPage($itemsPerPage) {
		$this->itemsPerPage = max((int)$itemsPerPage,1);
		$this->recalculate();
	}

	/**
	 * @param int $page
	 */
	public function setPage($page) {
		$this->page = max((int)$page,1);
	}

	/**
	 * @param string $name
	 */
	public function setParamName($name) {
		$this->paramName = $name;
	}

	/*** getters ***/

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getPageIndex() {
		return $this->page-1;
	}

	/**
	 * @return int
	 */
	public function getFirstPage() {
		return 1;
	}

	/**
	 * @return int
	 */
	public function getLastPage() {
		return $this->firstPage + $this->pageCount - 1;
	}

	/**
	 * @return int
	 */
	public function getNextPage() {
		return $this->page < $this->lastPage ? $this->page+1 : $this->page;
	}

	/**
	 * @return int
	 */
	public function getPrevPage() {
		return $this->page > $this->firstPage ? $this->page-1 : $this->page;
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return $this->pageCount;
	}

	/**
	 * @return int
	 */
	public function getItemCount() {
		return $this->itemCount;
	}

	/**
	 * @return int
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return ($this->page-1)*$this->itemsPerPage;
	}

	/**
	 * @return int
	 */
	public function getLength() {
		return min($this->itemsPerPage, $this->itemCount-$this->offset);
	}

	private function prepareView() {

		if ($this->pageCount < 2) {
			$steps = array($this->page);
		}
		else {
			$arr = range(max($this->firstPage, $this->page - 3), min($this->lastPage, $this->page + 3));
			$count = 4;
			$quotient = ($this->pageCount - 1) / $count;
			for ($i = 0; $i <= $count; $i++) {
				$arr[] = round($quotient * $i) + $this->firstPage;
			}
			sort($arr);
			$steps = array_values(array_unique($arr));
		}

		$stepsWithQueryStrings = array();

		for($i = 0; $i < count($steps); $i++) {
			$stepsWithQueryStrings[$steps[$i]] = $this->getQueryString($steps[$i]);
		}

		$this->getView()->steps = $stepsWithQueryStrings;
		$this->getView()->isFirst = $this->isFirst();
		$this->getView()->page = $this->getPage();
		$this->getView()->isLast = $this->isLast();
		$this->getView()->nextPageQueryString = $this->getQueryString($this->getNextPage());
		$this->getView()->prevPageQueryString = $this->getQueryString($this->getPrevPage());
		$this->getView()->showPaginator = ($this->pageCount > 1);

	}

	public function view() {

		$this->prepareView();

		return parent::view();
	}
}