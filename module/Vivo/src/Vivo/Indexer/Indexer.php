<?php
namespace Vivo\Indexer;

use Vivo\CMS\Model;

class Indexer
{

	public function execute(Query $query)
	{

	}

	public function save(Model\Entity $entity)
	{

	}

    public function delete($entityPath)
    {
        //TODO - check the path is not null
//        $path   = str_replace(' ', '\\ ', $path);
//        $query  = new \Vivo\Indexer\Query(
//            'DELETE Vivo\CMS\Model\Entity\path = :path OR Vivo\CMS\Model\Entity\path = :path/*');
//        $query->setParameter('path', $path);
//        $this->indexer->execute($query);
    }

    /**
     * @param Query $query
     * @return array
     */
    public function find(Query $query)
    {

    }
}
