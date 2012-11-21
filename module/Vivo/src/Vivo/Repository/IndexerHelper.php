<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\Entity;
use Vivo\Indexer\Document;
use Vivo\Indexer\Field;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Wildcard as WildcardQuery;
use Vivo\Indexer\Query\Boolean as BooleanQuery;
use Vivo\Indexer\Query\Term as TermQuery;
use Vivo\Repository\Exception;

/**
 * IndexerHelper
 * Generates indexer documents for entities, builds indexer queries based on entity properties
 */
class IndexerHelper
{
    /**
     * Creates an indexer document for the submitted entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @return \Vivo\Indexer\Document
     */
    public function createDocument(Entity $entity)
    {
        $doc    = new Document();
        //UUID (field type 'keyword')
        $doc->addField(new Field('uuid', $entity->getUuid(), true, true, false, false));
        //Path (field type 'keyword')
        $doc->addField(new Field('path', $entity->getPath(), true, true, false, false));
        //Entity type (field type 'keyword')
        $doc->addField(new Field('type', get_class($entity), true, true, false, false));
        return $doc;
    }

    /**
     * Builds and returns a query returning a whole subtree of documents beginning at the $entity
     * @param Entity|string $spec Either an Entity object or a path to an entity
     * @throws Exception\InvalidArgumentException
     * @return \Vivo\Indexer\Query\Boolean
     */
    public function buildTreeQuery($spec)
    {
        if (is_string($spec)) {
            $path   = $spec;
        } elseif ($spec instanceof Entity) {
            /** @var $spec Entity */
            $path   = $spec->getPath();
        } else {
            throw new Exception\InvalidArgumentException(sprintf('%s: Unsupported specification type', __METHOD__));
        }
        $entityTerm          = new IndexerTerm($path, 'path');
        $entityQuery         = new TermQuery($entityTerm);
        $descendantPattern   = new IndexerTerm($path . '/*', 'path');
        $descendantQuery     = new WildcardQuery($descendantPattern);
        $boolQuery           = new BooleanQuery();
        $boolQuery->addSubquery($entityQuery, null);
        $boolQuery->addSubquery($descendantQuery, null);
        return $boolQuery;
    }

    /**
     * Builds a term identifying the $entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @return \Vivo\Indexer\Term
     */
    public function buildEntityTerm(Entity $entity)
    {
        $term   = new IndexerTerm($entity->getUuid(), 'uuid');
        return $term;
    }
}