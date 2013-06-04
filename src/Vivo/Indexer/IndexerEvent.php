<?php
namespace Vivo\Indexer;

use Vivo\CMS\Model\Entity;
use Vivo\Indexer\DocumentInterface as IndexerDocInterface;

use Zend\EventManager\Event;

/**
 * IndexerEvent
 */
class IndexerEvent extends Event
{
    /**
     * Indexer events
     */
    const EVENT_INDEX_PRE       = 'index.pre';
    const EVENT_INDEX_POST      = 'index.post';
    const EVENT_INDEX_FAILED    = 'index_failed';

    /**
     * Entity
     * @var Entity
     */
    protected $entity;

    /**
     * Indexer document
     * @var IndexerDocInterface
     */
    protected $idxDoc;

    /**
     * Entity path
     * @var string
     */
    protected $entityPath;

    /**
     * Sets entity
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Returns entity
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets indexer document
     * @param \Vivo\Indexer\DocumentInterface $idxDoc
     */
    public function setIdxDoc($idxDoc)
    {
        $this->idxDoc = $idxDoc;
    }

    /**
     * Returns indexer document
     * @return \Vivo\Indexer\DocumentInterface
     */
    public function getIdxDoc()
    {
        return $this->idxDoc;
    }

    /**
     * Sets entity path
     * @param string $entityPath
     */
    public function setEntityPath($entityPath)
    {
        $this->entityPath = $entityPath;
    }

    /**
     * Returns entity path
     * @return string
     */
    public function getEntityPath()
    {
        if ($this->entity) {
            return $this->entity->getPath();
        }
        return $this->entityPath;
    }
}
