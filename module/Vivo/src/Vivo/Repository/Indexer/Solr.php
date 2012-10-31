<?php
namespace Vivo\Repository\Indexer;

use Vivo\CMS\Model;

class Solr implements IndexerInterface {

	public function __construct($params = array('host' => 'localhost', 'port' => 8180, 'path' => '/solr/')) {
		$this->service = $params; //new \Apache_Solr_Service($params['host'], $params['post'], $params['path']);
		print_r($this);
	}

	public function execute(Query $query) {
		print_r($query);
	}

	public function save(Model\Entity $entity) {

	}

	public function begin() { }

	public function commit() { }

	public function rollback() { }

}
