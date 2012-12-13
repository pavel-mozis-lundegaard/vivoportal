<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\Entity;
use Vivo\Indexer\Document;
use Vivo\Indexer\Field;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Wildcard as WildcardQuery;
use Vivo\Indexer\Query\BooleanOr;
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
        //UUID
        $doc->addField(new Field('uuid', $entity->getUuid()));
        //Path
        $doc->addField(new Field('path', $entity->getPath()));
        $entityClass    = get_class($entity);
        //Entity type
        $doc->addField(new Field('type', $entityClass));
        //TODO - a temporary solution - when entity descriptions are implemented as annotations or whatever, refactor!
        switch ($entityClass) {
            case 'Vivo\CMS\Model\Site':
                 /** @var $entity \Vivo\CMS\Model\Site  */
                //Hosts
                $doc->addField(new Field('hosts', $entity->getHosts()));
                break;
            default:
                //No other fields will be indexed for other entity types
                break;
        }
        return $doc;
    }

    /**
     * Builds and returns a query returning a whole subtree of documents beginning at the $entity
     * @param Entity|string $spec Either an Entity object or a path to an entity
     * @throws Exception\InvalidArgumentException
     * @return \Vivo\Indexer\Query\BooleanInterface
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
        $boolQuery           = new BooleanOr($entityQuery, $descendantQuery);
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