<?php
namespace Vivo\Repository\Indexer;

class Query {
	/**
	 * @var string
	 */
	private $query;
	/**
	 * @var int
	 */
	private $maxResults;
	/**
	 * @var int
	 */
	private $firstResult;
	/**
	 * @var array
	 */
	private $parameters = array();

	public function __construct($query) {
		$this->query = $query;
	}

	public function appendQuery($query) {
		$this->query.= $query;
	}

	public function setParameter($name, $parameter) {
		$this->parameters[$name] = $parameter;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * @param int $offset
	 * @return Vivo\Repository\Indexer\Query
	 */
	public function setFirstResult($offset) {
		$this->firstResult = $offset;
		return $this;
	}

	/**
	 * @param int $limit
	 * @return Vivo\Repository\Indexer\Query
	 */
	public function setMaxResults($limit) {
		$this->maxResults = $limit;
		return $this;
	}
}
