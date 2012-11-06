<?php
namespace Vivo\Repository\UnitOfWork;

use Vivo\CMS\Model;
use Vivo\IO;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\Storage\StorageInterface;
use Vivo\Uuid\GeneratorInterface as UuidGenerator;
use Vivo\IO\IOUtil;
use Vivo\Repository\Exception;

use Zend\Serializer\Adapter\AdapterInterface as Serializer;

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
    protected $saveStreams          = array();

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
     * List of temporary files which will be moved to their final place if everything is well in commit
     * Map: path => tempPath
     * @var string[]
     */
    protected $tmpFiles             = array();

    /**
     * List of temporary paths which will be deleted if everything is well in commit
     * Map: path => tempPath
     * @var string[]
     */
    protected $tmpDelFiles          = array();

    /**
     * PathBuilder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Storage
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    /**
     * Entity filename
     * @var string
     */
    protected $entityFilename;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var IOUtil
     */
    protected $ioUtil;

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
        $this->saveStreams[$path] = $stream;
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
     * Calling this function on uncommitted transaction may lead to data loss!
     * @return void
     */
    public function reset()
    {
        $this->copyFiles            = array();
        $this->deletePaths          = array();
        $this->saveData             = array();
        $this->saveEntities         = array();
        $this->saveStreams          = array();
        $this->removeTempFiles();
    }

    /**
     * Removes temporary files
     */
    protected function removeTempFiles()
    {
        //TempDelFiles
        foreach ($this->tmpDelFiles as $tmpPath) {
            try {
                $this->storage->remove($tmpPath);
            } catch (\Exception $e) {
                //Just continue, we should try to remove all temp files even though an exception has been thrown
            }
        }
        $this->tmpDelFiles          = array();
        //TempFiles
        foreach ($this->tmpFiles as $path) {
            try {
                $this->storage->remove($path);
            } catch (\Exception $e) {
                //Just continue, we should try to remove all temp files even though an exception has been thrown
            }
        }
        $this->tmpFiles             = array();
    }

    /**
     * Commits the current transaction and starts a new one
     */
    public function begin()
    {
        $this->commit();
        //A transaction is always open so there is no specific action to start a new one
    }

    /**
     * Rolls back current transaction and starts a new one
     */
    public function rollback()
    {
        //Move back everything that was moved during Delete Phase 1
        foreach ($this->tmpDelFiles as $path => $tmpPath) {
            try {
                $this->storage->move($tmpPath, $path);
            } catch (\Exception $e) {
                //Just continue
            }
        }
        //Reset also deletes remaining temp files
        $this->reset();
    }

    /**
     * Commit commits the current transaction, making its changes permanent, then starts a new transaction
     * @throws Exception\Exception
     */
    public function commit()
    {
        try {
            //Delete - Phase 1 (move to temp files)
            foreach ($this->deletePaths as $path) {
                $this->tmpDelFiles[$path]   = $this->pathBuilder->buildStoragePath(array(uniqid('del-')), true);
                $this->storage->move($path, $this->tmpDelFiles[$path]);
            }
            //Save - Phase 1 (serialize entities and files into temp files)
            //a) Entity
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
                $pathElements           = array($entity->getPath(), $this->entityFilename);
                $path                   = $this->pathBuilder->buildStoragePath($pathElements, true);
                $this->tmpFiles[$path]  = $path . '.' . uniqid('tmp-');
                $entitySer              = $this->serializer->serialize($entity);
                $this->storage->set($this->tmpFiles[$path], $entitySer);
            }
            //b) Data
            foreach ($this->saveData as $path => $data) {
                $this->tmpFiles[$path]  = $path . '.' . uniqid('tmp-');
                $this->storage->set($this->tmpFiles[$path], $data);
            }
            //c) Streams
            foreach ($this->saveStreams as $path => $stream) {
                $this->tmpFiles[$path]  = $path . '.' . uniqid('tmp-');
                $output                 = $this->storage->write($this->tmpFiles[$path]);
                $this->ioUtil->copy($stream, $output);
            }
            //Delete - Phase 2 (delete the temp files)
            foreach ($this->tmpDelFiles as $tmpDelFile) {
                $this->storage->remove($tmpDelFile);
            }
            //Save Phase 2 (rename temp files to real ones)
            foreach ($this->tmpFiles as $path => $tmpPath) {
                if (!$this->storage->move($tmpPath, $path)) {
                    throw new Exception\Exception(
                        sprintf("%s: Move failed; source: '%s', destination: '%s'", __METHOD__, $tmpPath, $path));
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
            $this->rollback();
            throw $e;
        }
    }
}