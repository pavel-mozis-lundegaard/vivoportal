<?php
namespace Vivo\Storage;

use Vivo\Storage\StorageInterface;
use Vivo\Storage\Exception;

/**
 * Stream wrapper for Storage
 * Based on Zend\View\Stream
 */
class StreamWrapper
{
    /**
     * Array of registered storages
     * Keys are stream names (protocols)
     * @var StorageInterface[]
     */
    protected static $storages;

    /**
     * Stream name / protocol
     * @var string
     */
    protected $streamName;

    /**
     * Path withing the storage
     * @var string
     */
    protected $barePath;

    /**
     * Storage
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Current stream position.
     * @var int
     */
    protected $pos = 0;

    /**
     * Data for streaming.
     * @var string
     */
    protected $data;

    /**
     * Stream stats.
     * @var array
     */
    protected $stat;

    /**
     * Loads file data from storage
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $storage    = $this->getStorageFromPath($path);
        $barePath   = $this->getBarePath($path);
        if ($storage->contains($barePath) && !$storage->isObject($barePath)) {
            //A directory
            throw new Exception\StreamException(sprintf("%s: '%s' is a directory, only files supported"));
        }
        $modeMain   = substr($mode, 0, 1);
        switch ($modeMain) {
            case 'r':
                if (!$storage->isObject($barePath)) {
                    $this->stat = false;
                    return false;
                }
                $this->data = $storage->get($barePath);
                $this->pos  = 0;
                break;
            case 'w':
                $storage->remove($barePath);
                $storage->touch($barePath);
                $this->data = '';
                $this->pos  = 0;
                break;
            case 'a':
                if (!$storage->isObject($barePath)) {
                    $storage->touch($barePath);
                }
                $this->data = $storage->get($barePath);
                $this->pos  = strlen($this->data);
                break;
            default:
                throw new Exception\StreamException(sprintf("%s: Mode '%s' not supported", __METHOD__, $mode));
                break;
        }
        $this->stat = array(
            'size'      => strlen($this->data),
            'mtime'     => $storage->mtime($barePath),
            'mode'      => 0100 + 0200 + 0400,  //Execute, write and read permissions
        );
        $this->streamName   = $this->getStreamNameFromPath($path);
        $this->barePath     = $barePath;
        $this->storage      = $storage;
        return true;
    }

    public function stream_close()
    {
        $this->stream_flush();
    }

    public function stream_flush()
    {
        $this->storage->set($this->barePath, $this->data);
    }

    /**
     * Reads from the stream.
     */
    public function stream_read($count)
    {
        $ret = substr($this->data, $this->pos, $count);
        $this->pos += strlen($ret);
        return $ret;
    }


    /**
     * Tells the current position in the stream.
     */
    public function stream_tell()
    {
        return $this->pos;
    }


    /**
     * Tells if we are at the end of the stream.
     */
    public function stream_eof()
    {
        return $this->pos >= strlen($this->data);
    }


    /**
     * Stream statistics.
     */
    public function stream_stat()
    {
        return $this->stat;
    }


    /**
     * Seek to a specific point in the stream.
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->data) && $offset >= 0) {
                    $this->pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->data) + $offset >= 0) {
                    $this->pos = strlen($this->data) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    public function stream_write($data)
    {
        $length     = strlen($data);
        $this->data = substr_replace($this->data, $data, $this->pos, $length);
        $this->pos  += $length;

        //TODO - flushing is necessary when multiple file handles are used
        $this->stream_flush();

        $this->stat = array(
            'size'      => strlen($this->data),
            'mtime'     => $this->storage->mtime($this->barePath),
            'mode'      => 0100 + 0200 + 0400,  //Execute, write and read permissions
        );

        return $length;
    }

    public function stream_lock($mode)
    {
        //Locking not implemented
        return true;
    }

    /**
     * Retrieve information about a file
     * @link http://cz.php.net/manual/en/streamwrapper.url-stat.php
     * @param string $path
     * @param int $flags
     * @throws Exception\StreamException
     * @return array
     */
    public function url_stat($path = null, $flags = null)
    {
        if (is_null($path)) {
            throw new Exception\StreamException(sprintf('%s: Path cannot be null', __METHOD__));
        }
        $storage    = $this->getStorageFromPath($path);
        $barePath   = $this->getBarePath($path);
        if ($storage->isObject($barePath)) {
            //A file
            $stat   = array(
                'mtime'     => $storage->mtime($barePath),
                'size'      => $storage->size($barePath),
                'mode'      => 0100 + 0200 + 0400,  //Execute, write and read permissions
            );
        } elseif ($storage->contains($barePath)) {
            //A dir
            $stat   = array(
                'mode'      => 040000,  //Directory flag
            );
        } else {
            //Not found
            $stat   = false;
        }
        return $stat;
    }

    public function unlink($path)
    {
        if (is_null($path)) {
            throw new Exception\StreamException(sprintf('%s: Path cannot be null', __METHOD__));
        }
        $storage    = $this->getStorageFromPath($path);
        $barePath   = $this->getBarePath($path);
        $success    = $storage->remove($barePath);
        //This method must return boolean!
        $retVal     = (bool)$success;
        return $retVal;
    }

    /**
     * Returns name of the stream (protocol)
     * @param string $path
     * @return string
     */
    protected function getStreamNameFromPath($path)
    {
        $components = explode('://', $path);
        return $components[0];
    }

    /**
     * Returns storage object associated with the stream (protocol) present in the path
     * @param string $path
     * @return StorageInterface
     */
    protected function getStorageFromPath($path)
    {
        $streamName = $this->getStreamNameFromPath($path);
        return self::$storages[$streamName];
    }

    /**
     * Returns the path without the stream name and ://
     * @param string $path
     * @return string
     */
    protected function getBarePath($path)
    {
        $components = explode('://', $path);
        return $components[1];
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
            throw new Exception\StreamException(sprintf("%s: Stream name not set", __METHOD__));
        }
        if (!stream_wrapper_register($streamName, __CLASS__)) {
            throw new Exception\StreamException(sprintf("%s: Registration of stream '%s' failed.", __METHOD__, $streamName));
        }
        self::$storages[$streamName]    = $storage;
    }
}
