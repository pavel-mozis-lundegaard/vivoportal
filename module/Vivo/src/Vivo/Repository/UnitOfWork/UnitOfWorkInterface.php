<?php
namespace Vivo\Repository\UnitOfWork;

use Vivo\CMS\Model;
use Vivo\IO;

/**
 * UnitOfWorkInterface
 */
interface UnitOfWorkInterface
{
    /**
     * Adds the entity to the list of entities to be saved
     * @param \Vivo\CMS\Model\Entity $entity
     * @return void
     */
    public function saveEntity(Model\Entity $entity);

    /**
     * Adds the stream to the list of streams to be saved
     * @param \Vivo\IO\InputStreamInterface $stream
     * @param string $path
     * @return void
     */
    public function saveStream(IO\InputStreamInterface $stream, $path);

    /**
     * Adds data to the list of data items to be saved
     * @param string $data
     * @param string $path
     * @return void
     */
    public function saveData($data, $path);

}