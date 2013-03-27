<?php
namespace Vivo\CMS\Indexer;

use Vivo\CMS\Model\Entity;

/**
 * IndexerHelperInterface
 */
interface IndexerHelperInterface
{
    /**
     * Creates an indexer document for the submitted entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @param mixed|null $options Various options required to index the entity
     * @throws Exception\MethodNotFoundException
     * @return \Vivo\Indexer\Document
     */
    public function createDocument(Entity $entity, array $options = array());

    /**
     * Builds and returns a query returning a whole subtree of documents beginning at the $entity
     * @param Entity|string $spec Either an Entity object or a path to an entity
     * @throws Exception\InvalidArgumentException
     * @return \Vivo\Indexer\Query\BooleanInterface
     */
    public function buildTreeQuery($spec);

    /**
     * Builds a term identifying the $entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @return \Vivo\Indexer\Term
     */
    public function buildEntityTerm(Entity $entity);

}
