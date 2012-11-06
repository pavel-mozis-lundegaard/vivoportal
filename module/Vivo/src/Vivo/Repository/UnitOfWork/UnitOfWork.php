<?php
namespace Vivo\Repository\UnitOfWork;

use Vivo\CMS\Model;
use Vivo\IO;

/**
 * UnitOfWork
 */
class UnitOfWork implements UnitOfWorkInterface
{
    /**
     * List of entities that are prepared to be persisted
     * @var Model\Entity[]
     */
    protected $saveEntities     = array();

    /**
     * List of (resource) files that are prepared to be persisted
     * //TODO - are these only streams? If so, rename
     * @var array
     */
    protected $saveFiles        = array();

    /**
     * List of data items prepared to be persisted
     * @var string[]
     */
    protected $saveData         = array();

    /**
     * List of files that are prepared to be copied
     * @var array
     */
    protected $copyFiles        = array();

    /**
     * List of resource files and entities that are prepared to be deleted
     * @var array
     */
    protected $deletePaths      = array();

    /**
     * List of entities that are prepared to be deleted
     * @var array
     */
    protected $deleteEntities   = array();

    /**
     * Adds the entity to the list of entities to be saved
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function saveEntity(Model\Entity $entity)
    {
        $path                       = $entity->getPath();
        $this->saveEntities[$path]  = $entity;
    }

    /**
     * Adds the stream to the list of streams to be saved
     * @param \Vivo\IO\InputStreamInterface $stream
     * @param string $path
     * @return void
     */
    public function saveStream(IO\InputStreamInterface $stream, $path)
    {
        $this->saveFiles[$path] = $stream;
    }

    /**
     * Adds data to the list of data items to be saved
     * @param string $data
     * @param string $path
     * @return void
     */
    public function saveData($data, $path)
    {
        $this->saveData[$path]  = $data;
    }
}