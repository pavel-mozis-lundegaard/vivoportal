<?php
namespace Vivo\Repository\UuidConvertor;

use Vivo\Indexer\Indexer;
use Vivo\Indexer\Query as IndexerQuery;

/**
 * UuidConvertor
 * Converts from/to UUID to/from path
 * Caches results
 */
class UuidConvertor implements UuidConvertorInterface
{
    /**
     * Indexer instance
     * @var Indexer
     */
    protected $indexer;

    /**
     * Maps UUID => path
     * Result cache
     * @var string[]
     */
    protected $uuidToPath   = array();

    /**
     * Maps path => UUID
     * Result cache
     * @var string[]
     */
    protected $pathToUuid   = array();

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
        if (isset($this->pathToUuid[$path])) {
            $uuid   = $this->pathToUuid[$path];
        } else {
            //TODO - build indexer query
            $query  = new IndexerQuery('...');
            $query->setParameter('path', $path);
            $docs   = $this->indexer->execute($query);
            if ($docs) {
                $doc    = $docs[0];
                $uuid   = $doc['uuid'];
                $this->addToResultCache($uuid, $path);
            } else {
                $uuid   = null;
            }
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
        if (isset($this->uuidToPath[$uuid])) {
            $path   = $this->uuidToPath[$uuid];
        } else {
            //TODO - build indexer query
            $query  = new IndexerQuery('...');
            $query->setParameter('uuid', $uuid);
            $docs   = $this->indexer->execute($query);
            if ($docs) {
                $doc    = $docs[0];
                $path   = $doc['path'];
                $this->addToResultCache($uuid, $path);
            } else {
                $path   = null;
            }
        }
        return $path;
    }

    /**
     * Caches conversion results
     * @param string $uuid
     * @param string $path
     */
    public function addToResultCache($uuid, $path)
    {
        $this->uuidToPath[$uuid]    = $path;
        $this->pathToUuid[$path]    = $uuid;
    }

    /**
     * Removes a uuid and its associated path from the result cache
     * @param string $uuid
     */
    public function removeFromResultCacheByUuid($uuid)
    {
        if (isset($this->uuidToPath[$uuid])) {
            $path   = $this->uuidToPath[$uuid];
            unset($this->uuidToPath[$uuid]);
            unset($this->pathToUuid[$path]);
        }
    }

    /**
     * Removes a path and its associated uuid from the result cache
     * @param string $path
     */
    public function removeFromResultCacheByPath($path)
    {
        if (isset($this->pathToUuid[$path])) {
            $uuid   = $this->pathToUuid[$path];
            unset($this->pathToUuid[$path]);
            unset($this->uuidToPath[$uuid]);
        }
    }
}