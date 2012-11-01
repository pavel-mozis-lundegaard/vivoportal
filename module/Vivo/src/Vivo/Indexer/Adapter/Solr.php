<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Query;
use Vivo\CMS\Model;

class Solr implements AdapterInterface
{

	public function __construct($params = array('host' => 'localhost', 'port' => 8180, 'path' => '/solr/'))
	{
		$this->service = $params;
	}

	public function execute(Query $query)
	{

	}

	public function save(Model\Entity $entity)
	{

	}

	public function begin() { }

	public function commit() { }

	public function rollback() { }

}
