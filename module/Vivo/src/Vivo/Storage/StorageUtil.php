<?php
namespace Vivo\Storage;

use Vivo\Storage\Exception;
use Vivo\IO\IOUtil;

/**
 * StorageUtil
 */
class StorageUtil
{
    //TODO - replace all '/'

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
     * Can copy directories as well as files
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
        if ($storageFrom->isObject($pathFrom)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: '%s' is not a directory in source storage", __METHOD__, $pathFrom));
        }
        if ($storageTo->isObject($pathTo)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: '%s' is not a directory in target storage", __METHOD__, $pathTo));
        }
        $scan   = $storageFrom->scan($pathFrom);
        foreach ($scan as $path) {
            $fullSrcPath    = $pathFrom . '/' . $path;
            $fullTargetPath = $pathTo . '/' . $path;
            if ($storageFrom->isObject($fullSrcPath)) {
                //A file
                $this->copyFile($storageFrom, $fullSrcPath, $storageTo, $fullTargetPath);
            } else {
                //A directory
                $this->copy($storageFrom, $fullSrcPath, $storageTo, $fullTargetPath);
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
            throw new Exception\InvalidArgumentException(sprintf("%s: '%s' is not a file in source storage", __METHOD__, $pathFrom));
        }
        if (!$storageTo->isObject($pathTo)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: '%s' is not a file in target storage", __METHOD__, $pathTo));
        }
        $inputStream    = $storageFrom->read($pathFrom);
        $outputStream   = $storageTo->write($pathTo);
        $this->ioUtil->copy($inputStream, $outputStream);
    }
}