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
    protected $saveEntities         = array();

    /**
     * List of streams that are prepared to be persisted
     * @var IO\InputStreamInterface[]
     */
    protected $saveFiles            = array();

    /**
     * List of data items prepared to be persisted
     * @var string[]
     */
    protected $saveData             = array();

    /**
     * List of files that are prepared to be copied
     * @var array
     */
    protected $copyFiles            = array();

    /**
     * List of paths that are prepared to be deleted
     * @var string[]
     */
    protected $deletePaths          = array();

    /**
     * List of paths of entities that are prepared to be deleted
     * @var string[]
     */
    protected $deleteEntityPaths    = array();

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

    /**
     * Adds the entity to the list of entities to be deleted
     * @param \Vivo\CMS\Model\Entity $entity
     * @return void
     */
    public function deleteEntity(Model\Entity $entity)
    {
        $this->deleteEntityPaths[]  = $entity->getPath();
        $this->deletePaths[]        = $entity->getPath();
    }

    /**
     * Deletes an item (object) specified by $path
     * @param string $path
     * @return mixed
     */
    public function deleteItem($path)
    {
        $this->deletePaths[]    = $path;
    }

    /**
     * Resets / clears all lists of entities / data scheduled for processing
     * @return void
     */
    public function reset()
    {
        $this->copyFiles            = array();
        $this->deleteEntityPaths    = array();
        $this->deletePaths          = array();
        $this->saveData             = array();
        $this->saveEntities         = array();
        $this->saveFiles            = array();
    }

    /**
     * Commit commits the current transaction, making its changes permanent.
     * @throws Exception
     */
    public function commit()
    {
        $tmpFiles       = array();
        $tmpDelFiles    = array();
        try {
            //Delete - Phase 1 (move to temp files)
            try {
                foreach ($this->deletePaths as $path) {
                    $tmpDelFiles[$path] = $this->pathBuilder->buildStoragePath(array(uniqid('del-')), true);
                    $this->storage->move($path, $tmpDelFiles[$path]);
                }
            }
            catch (\Exception $e) {
                //Move back everything that was moved
                foreach ($tmpDelFiles as $path => $tmpPath)
                    $this->storage->move($tmpPath, $path);
                throw $e;
            }
            //Save - Phase faze 1 (serialize entities and files into temp files)
            //a) entity
            $now = new \DateTime();
            foreach ($this->saveEntities as $entity) {
                if (!$entity->getCreated() instanceof \DateTime) {
                    $entity->setCreated($now);
                }
                if (!$entity->getCreatedBy()) {
                    //TODO - what to do when an entity does not have its creator set?
                    //$entity->setCreatedBy($username);
                }
                if(!$entity->getUuid()) {
                    $entity->setUuid($this->uuidGenerator->create());
                }
                $entity->setModified($now);
                //TODO - set entity modifier
                //$entity->setModifiedBy($username);
                $pathElements       = array($entity->getPath(), self::ENTITY_FILENAME);
                $path               = $this->pathBuilder->buildStoragePath($pathElements, true);
                $tmpPath            = $path . '.' . uniqid('tmp-');
                $entitySer          = $this->serializer->serialize($entity);
                $this->storage->set($tmpPath, $entitySer);
                $tmpFiles[$path]    = $tmpPath;
            }
            foreach ($this->saveData as $path => $data) {
                $tmpPath            = $path . '.' . uniqid('tmp-');
                $this->storage->set($tmpPath, $data);
                $tmpFiles[$path] = $tmpPath;
            }
            //b) Resource files
            foreach ($this->saveFiles as $path => $stream) {
                $tmpPath    = $path . '.' . uniqid('tmp-');
                //TODO - a bug? Shouldn't be ->write($tmpPath)?
                $output     = $this->storage->write($path);
                $this->ioUtil->copy($stream, $output);
                $tmpFiles[$path] = $tmpPath;
            }
            //Delete - Phase 2 (delete the temp files)
            foreach ($tmpDelFiles as $tmpDelFile)
                $this->storage->remove($tmpDelFile);
            //Save Phase 2 (rename temp files to real ones)
            foreach ($tmpFiles as $path => $tmpPath) {
                if (!$this->storage->move($tmpPath, $path)) {
                    throw new Exception\Exception(
                        sprintf("%s: Commit failed; source: '%s', destination: '%s'", __METHOD__, $tmpPath, $path));
                }
            }
            //TODO - delete entities from index?
// 			foreach ($this->deleteEntities as $path) {
// 				$path = str_replace(' ', '\\ ', $path);
// 				$query = new Indexer\Query('DELETE Vivo\CMS\Model\Entity\path = :path OR Vivo\CMS\Model\Entity\path = :path/*');
// 				$query->setParameter('path', $path);

// 				$this->indexer->execute($query);
// 			}
// 			foreach ($this->saveEntities as $entity)
// 				$this->indexer->save($entity); // (re)index entity
// 			$this->indexer->commit();
            $this->reset();
        } catch (\Exception $e) {
            //Delete all temp files created during commit
            foreach ($tmpFiles as $path)
                $this->storage->remove($path);
            // ...and empty the dirty entities and files array (done by rollback)
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Commits the current transaction and starts a new one
     */
    public function begin()
    {
        $this->commit();
        //A transaction is always open so there is no specific action to start a new one
    }

    public function rollback()
    {
        // TODO: Implement rollback() method.
    }
}