<?php
namespace Vivo\CMS\Indexer;

class Reindexer implements ReindexerInterface
{
    public function delete()
    {
//        $delQuery   = $this->indexerHelper->buildTreeQuery($entity);
//        $this->indexer->delete($delQuery);
    }

    public function save()
    {
        //Indexer - remove old doc & insert new one
//        $entityTerm = $this->indexerHelper->buildEntityTerm($entity);
//        $delQuery   = new TermQuery($entityTerm);
//        $entityDoc  = $this->indexerHelper->createDocument($entity, $entityOptions);
//        $this->indexer->delete($delQuery);
//        $this->indexer->addDocument($entityDoc);

    }
}
