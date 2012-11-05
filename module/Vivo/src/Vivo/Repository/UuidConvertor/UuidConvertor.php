<?php
namespace Vivo\Repository\UuidConvertor;

use Vivo\Indexer\Indexer;
use Vivo\Indexer\Query as IndexerQuery;

/**
 * UuidConvertor
 * Converts from/to UUID to/from path
 */
class UuidConvertor implements UuidConvertorInterface
{
    /**
     * Indexer instance
     * @var Indexer
     */
    protected $indexer;

    /**
     * Constructor
     * @param \Vivo\Indexer\Indexer $indexer
     */
    public function __construct(Indexer $indexer)
    {
        $this->indexer  = $indexer;
    }

    /**
     * Returns entity UUID based on its path
     * If the entity does not exist returns null
     * @param string $path
     * @return null|string
     */
    public function getUuidFromPath($path)
    {
        //TODO - build indexer query
        $query  = new IndexerQuery('...');
        $query->setParameter('path', $path);
        $docs   = $this->indexer->execute($query);
        if ($docs) {
            $doc    = $docs[0];
            $uuid   = $doc['uuid'];
        } else {
            $uuid   = null;
        }
        return $uuid;
    }

    /**
     * Returns entity path based on its UUID
     * If the UUID does not exist returns null
     * @param string $uuid
     * @return null|string
     */
    public function getPathFromUuid($uuid)
    {
        //TODO - build indexer query
        $query  = new IndexerQuery('...');
        $query->setParameter('uuid', $uuid);
        $docs   = $this->indexer->execute($query);
        if ($docs) {
            $doc    = $docs[0];
            $path   = $doc['path'];
        } else {
            $path   = null;
        }
        return $path;
    }
}