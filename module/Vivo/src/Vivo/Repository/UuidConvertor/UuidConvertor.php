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
    public function getUuid($path)
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
                $this->set($uuid, $path);
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
    public function getPath($uuid)
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
                $this->set($uuid, $path);
            } else {
                $path   = null;
            }
        }
        return $path;
    }

    /**
     * Sets a conversion result (uuid and its associated path) into the result cache
     * Overwrites previously cached results
     * @param string $uuid
     * @param string $path
     */
    public function set($uuid, $path)
    {
        $this->uuidToPath[$uuid]    = $path;
        $this->pathToUuid[$path]    = $uuid;
    }

    /**
     * Removes a conversion result (uuid and its associated path) from the result cache
     * If $uuid is not found in cached results, does nothing
     * @param string $uuid
     */
    public function removeByUuid($uuid)
    {
        if (isset($this->uuidToPath[$uuid])) {
            $path   = $this->uuidToPath[$uuid];
            unset($this->uuidToPath[$uuid]);
            unset($this->pathToUuid[$path]);
        }
    }

    /**
     * Removes a conversion result (path and its associated uuid) from the result cache
     * If $path is not found in cached results, does nothing
     * @param string $path
     */
    public function removeByPath($path)
    {
        if (isset($this->pathToUuid[$path])) {
            $uuid   = $this->pathToUuid[$path];
            unset($this->pathToUuid[$path]);
            unset($this->uuidToPath[$uuid]);
        }
    }
}