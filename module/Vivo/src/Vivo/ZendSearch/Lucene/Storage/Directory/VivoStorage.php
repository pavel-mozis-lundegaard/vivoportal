<?php
namespace Vivo\ZendSearch\Lucene\Storage\Directory;

use Vivo\Storage\StorageInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\ZendSearch\Lucene\Storage\File\StorageHandle;

use ZendSearch\Lucene\Storage\Directory\DirectoryInterface;

/**
 * VivoStorage
 * Directory implementation over Vivo Storage for ZendSearch\Lucene
 */
class VivoStorage implements DirectoryInterface
{
    /**
     * Storage
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Path in storage where the Lucene directory is placed
     * @var string
     */
    protected $path;

    /**
     * PathBuilder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var StorageHandle[]
     */
    protected $storageHandles   = array();

    /**
     * Constructor
     * @param \Vivo\Storage\StorageInterface $storage
     * @param string $path Path in storage where the Lucene directory is placed
     */
    public function __construct(StorageInterface $storage, $path)
    {
        $this->storage      = $storage;
        $this->path         = $path;
        $this->pathBuilder  = $storage->getPathBuilder();
    }

    /**
     * Closes the store.
     * @return void
     */
    public function close()
    {
        foreach ($this->storageHandles as $storageHandle) {
            $storageHandle->flush();
            $storageHandle->close();
        }
        $this->storageHandles   = array();
    }

    /**
     * Returns an array of strings, one for each file in the directory.
     * @return array
     */
    public function fileList()
    {
        $items  = $this->storage->scan($this->path);
        $files  = array();
        foreach ($items as $item) {
            $fullPath       = $this->getFullPath($item);
            if ($this->storage->isObject($fullPath)) {
                $files[]    = $item;
            }
        }
        return $files;
    }

    /**
     * Creates a new, empty file in the directory with the given $filename.
     * @param string $filename
     * @return StorageHandle
     */
    public function createFile($filename)
    {
        $this->deleteFile($filename);
        $fullPath                           = $this->getFullPath($filename);
        $this->storage->touch($fullPath);
        $storageHandle                      = new StorageHandle($this->storage, $fullPath);
        $this->storageHandles[$filename]    = $storageHandle;
        return $storageHandle;
    }

    /**
     * Removes an existing $filename in the directory.
     * @param string $filename
     * @return void
     */
    public function deleteFile($filename)
    {
        $this->purgeFile($filename);
        $fullPath   = $this->getFullPath($filename);
        if ($this->storage->isObject($fullPath)) {
            $this->storage->remove($fullPath);
        }
    }

    /**
     * Purge file if it's cached by directory object
     * Method is used to prevent 'too many open files' error
     * @param string $filename
     * @return void
     */
    public function purgeFile($filename)
    {
        if (isset($this->storageHandles[$filename])) {
            $this->storageHandles[$filename]->flush();
            $this->storageHandles[$filename]->close();
            unset($this->storageHandles[$filename]);
        }
    }

    /**
     * Returns true if a file with the given $filename exists.
     * @param string $filename
     * @return boolean
     */
    public function fileExists($filename)
    {
        if (isset($this->storageHandles[$filename])) {
            return true;
        }
        $fullPath   = $this->getFullPath($filename);
        if ($this->storage->isObject($fullPath)) {
            return true;
        }
        return false;
    }

    /**
     * Returns the length of a $filename in the directory.
     * @param string $filename
     * @return integer
     */
    public function fileLength($filename)
    {
        $fullPath   = $this->getFullPath($filename);
        $length     = $this->storage->size($fullPath);
        return $length;
    }

    /**
     * Returns the UNIX timestamp $filename was last modified.
     * @param string $filename
     * @return integer
     */
    public function fileModified($filename)
    {
        $fullPath   = $this->getFullPath($filename);
        $mtime      = $this->storage->mtime($fullPath);
        return $mtime;
    }

    /**
     * Renames an existing file in the directory.
     * @param string $from
     * @param string $to
     * @return void
     */
    public function renameFile($from, $to)
    {
        $this->purgeFile($from);
        $this->purgeFile($to);
        $fullPathTo     = $this->getFullPath($to);
        $fullPathFrom   = $this->getFullPath($from);
        if ($this->storage->isObject($fullPathTo)) {
            $this->storage->remove($fullPathTo);
        }
        $this->storage->move($fullPathFrom, $fullPathTo);
    }

    /**
     * Sets the modified time of $filename to now.
     * @param string $filename
     * @return void
     */
    public function touchFile($filename)
    {
        $fullPath   = $this->getFullPath($filename);
        $this->storage->touch($fullPath);
    }

    /**
     * Returns a Vivo\ZendSearch\Lucene\Storage\File\VivoStorage object for a given $filename in the directory.
     * If $shareHandler option is true, then file handler can be shared between File Object
     * requests. It speed-ups performance, but makes problems with file position.
     * Shared handler are good for short atomic requests.
     * Non-shared handlers are useful for stream file reading (especial for compound files).
     * @param string $filename
     * @param boolean $shareHandler
     * @throws \ZendSearch\Lucene\Exception\InvalidArgumentException
     * @return StorageHandle
     */
    public function getFileObject($filename, $shareHandler = true)
    {
        $fullPath = $this->getFullPath($filename);
        //Index is not created without this exception
        if (!$this->storage->isObject($fullPath)) {
            throw new \ZendSearch\Lucene\Exception\InvalidArgumentException(
                'File \'' . $filename . '\' is not readable.');
        }
        if (!$shareHandler) {
            $storageHandle  = new StorageHandle($this->storage, $fullPath);
            return $storageHandle;
        }
        if (isset($this->storageHandles[$filename])) {
            $this->storageHandles[$filename]->seek(0);
            return $this->storageHandles[$filename];
        }
        $storageHandle  = new StorageHandle($this->storage, $fullPath);
        $this->storageHandles[$filename]    = $storageHandle;
        return $storageHandle;
    }

    /**
     * Returns full path for a given filename
     * @param string $filename
     * @return string
     */
    protected function getFullPath($filename)
    {
        $components = array($this->path, $filename);
        $fullPath   = $this->pathBuilder->buildStoragePath($components, true);
        return $fullPath;
    }
}