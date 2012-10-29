<?php
namespace Vivo\Storage;

use Vivo\Storage\Exception;
use Vivo\IO\IOUtil;

/**
 * StorageUtil
 */
class StorageUtil
{
    /**
     * IO Utilities
     * @var IOUtil
     */
    protected $ioUtil;

    /**
     * Constructor
     * @param \Vivo\IO\IOUtil $ioUtil
     */
    public function __construct(IOUtil $ioUtil)
    {
        $this->ioUtil   = $ioUtil;
    }

    /**
     * Copies data between storages
     * Copies directories as well as files
     * @param StorageInterface $storageFrom
     * @param string $pathFrom
     * @param StorageInterface $storageTo
     * @param string $pathTo
     */
    public function copy(StorageInterface $storageFrom, $pathFrom, StorageInterface $storageTo, $pathTo)
    {
        if ($storageFrom->isObject($pathFrom)) {
            $this->copyFile($storageFrom, $pathFrom, $storageTo, $pathTo);
        } else {
            $this->copyDir($storageFrom, $pathFrom, $storageTo, $pathTo);
        }
    }

    /**
     * Copies a directory between storages
     * @param StorageInterface $storageFrom
     * @param string $pathFrom
     * @param StorageInterface $storageTo
     * @param string $pathTo The directory to copy the *contents* of $pathFrom to
     * @throws Exception\InvalidArgumentException
     */
    protected function copyDir(StorageInterface $storageFrom, $pathFrom, StorageInterface $storageTo, $pathTo)
    {
        if (!$storageFrom->contains($pathFrom)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Path '%s' does not exist in source storage",
                                                            __METHOD__, $pathFrom));
        }
        if ($storageFrom->isObject($pathFrom)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Path '%s' is not a directory in source storage",
                                                            __METHOD__, $pathFrom));
        }
        $scan   = $storageFrom->scan($pathFrom);
        foreach ($scan as $path) {
            $fullSrcPath    = $storageFrom->buildStoragePath(array($pathFrom, $path), true);
            $fullTargetPath = $storageTo->buildStoragePath(array($pathTo, $path), true);
            if ($storageFrom->isObject($fullSrcPath)) {
                //A file
                $this->copyFile($storageFrom, $fullSrcPath, $storageTo, $fullTargetPath);
            } else {
                //A directory
                $this->copyDir($storageFrom, $fullSrcPath, $storageTo, $fullTargetPath);
            }
        }
    }

    /**
     * Copies a file between storages
     * @param StorageInterface $storageFrom
     * @param string $pathFrom
     * @param StorageInterface $storageTo
     * @param string $pathTo The *filename* (not directory) to copy to
     * @throws Exception\InvalidArgumentException
     */
    protected function copyFile(StorageInterface $storageFrom, $pathFrom, StorageInterface $storageTo, $pathTo)
    {
        if (!$storageFrom->isObject($pathFrom)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: '%s' is not a file in source storage",
                                                            __METHOD__, $pathFrom));
        }
        $inputStream    = $storageFrom->read($pathFrom);
        $outputStream   = $storageTo->write($pathTo);
        $this->ioUtil->copy($inputStream, $outputStream);
    }
}