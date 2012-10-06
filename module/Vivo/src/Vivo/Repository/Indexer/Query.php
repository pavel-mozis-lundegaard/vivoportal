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

	const HYDRATE_FIELD_LIST = 'field_list';
	const HYDRATE_FACET_FIELD_LIST = 'facet_field_list';
	const HYDRATE_ENTITY_LIST = 'entity_list';

	private $hydrateMode;

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

	public function setHydrateMode($mode) {
		//@todo: overeni na existujici hydr
		$this->hydrateMode = $mode;
	}

// 	/**
// 	 * X vysledku
// 	 */
// 	public function getResult() { }
// 	/*
// 	 * 1 vysldek
// 	 */
// 	public function getSingleResult() { }
// 	/**
// 	 * bnic nevraci, napr. volani delete
// 	 */
// 	public function execute() { }


}
