<?php
namespace Vivo\Vmodule;

use Zend\View\Stream as ZendViewStream;
use Vivo\Storage\StorageInterface;
use Vivo\Vmodule\Exception\StreamException;

/**
 * Stream wrapper to read Vmodule source files from storage
 * Based on Zend\View\Stream
 * @author david.lukas
 */
class StreamWrapper extends ZendViewStream
{
    /**
     * Name of the stream (protocol)
     * @var string
     */
    const STREAM_NAME   = 'vmodule';

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
        if (!self::$storage) {
            throw new StreamException(sprintf('%s: A Storage must be set.', __METHOD__));
        }
        //Get the source
        $path        = str_replace(self::STREAM_NAME . '://', '', $path);
        if (self::$storage->isObject($path)) {
            $this->data   = self::$storage->get($path);
        }
        if (($this->data === false) || is_null($this->data)) {
            $this->stat = false;
            return false;
        }
        //Update stat info
        $fileSize   = strlen($this->data);
        $this->stat = array(
            7   => $fileSize,
            'size'  => $fileSize,
        );
        return true;
    }

    /**
     * Registers this stream wrapper
     * @param \Vivo\Storage\StorageInterface|null $storage
     * @throws Exception\StreamException
     * @return void
     */
    public static function register(StorageInterface $storage = null)
    {
        if ($storage) {
            self::setStorage($storage);
        }
        if (!stream_wrapper_register(self::STREAM_NAME, __CLASS__)) {
            throw new StreamException(sprintf("%s: Registration of stream '%s' failed.", __METHOD__, self::STREAM_NAME));
        }
    }

    /**
     * Statically sets storage to use for Vmodules
     * @param \Vivo\Storage\StorageInterface $storage
     */
    public static function setStorage(StorageInterface $storage)
    {
        self::$storage  = $storage;
    }
}
