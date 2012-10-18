<?php
namespace Vivo\Module;

use Zend\View\Stream as ZendViewStream;
use Vivo\Storage\StorageInterface;
use Vivo\Module\Exception\StreamException;

/**
 * Stream wrapper to read module source files from storage
 * Based on Zend\View\Stream
 */
class StreamWrapper extends ZendViewStream
{
    /**
     * Name of the stream (protocol) for Vmodule source access
     * @var string
     */
    protected static $streamName;

    /**
     * Storage with Vmodules
     * @var StorageInterface
     */
    protected static $storage;

    /**
     * Loads file data from storage
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        //Get the source
        $path        = str_replace(self::$streamName . '://', '', $path);
        if (self::$storage->isObject($path)) {
            $this->data   = self::$storage->get($path);
        }
        if (($this->data === false) || is_null($this->data)) {
            $this->stat = false;
            return false;
        }
        //Update stat info
        $fileSize   = strlen($this->data);
        //TODO - other stat info?
        $this->stat = array(
            7   => $fileSize,
            'size'  => $fileSize,
        );
        return true;
    }

    /**
     * Registers this stream wrapper
     * @param string $streamName Name of the stream (protocol) used to access the Vmodule source
     * @param \Vivo\Storage\StorageInterface $storage
     * @throws Exception\StreamException
     * @return void
     */
    public static function register($streamName, StorageInterface $storage)
    {
        if (!$streamName) {
            throw new StreamException(sprintf("%s: Stream name not set", __METHOD__));
        }
        self::$streamName   = $streamName;
        self::$storage      = $storage;
        if (!stream_wrapper_register($streamName, __CLASS__)) {
            throw new StreamException(sprintf("%s: Registration of stream '%s' failed.", __METHOD__, $streamName));
        }
    }
}
