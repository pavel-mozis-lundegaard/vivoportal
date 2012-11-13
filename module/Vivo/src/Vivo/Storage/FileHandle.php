<?php
namespace Vivo\Storage;

class FileHandle
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Path to a file in storage
     * @var string
     */
    protected $path;

    /**
     * File pointer position
     * @var int
     */
    protected $position = 0;

    /**
     * Constructor
     * @param StorageInterface $storage
     * @param string $path
     */
    public function __construct(StorageInterface $storage, $path)
    {
        $this->storage  = $storage;
        $this->path     = $path;
        $this->position = 0;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return integer 0 on success, -1 on failure
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_SET:
                $this->position = $offset;
                $retval = true;
                break;

            case SEEK_CUR:
                $this->position += $offset;
                $retval = true;
                break;

            case SEEK_END:
                $this->position = $this->storage->size($this->path);
                $this->position += $offset;
                $retval = true;
                break;

            default:
                $retval = false;
                break;
        }
        return $retval;
    }

    /**
     * @return integer|boolean Returns false on error
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * @return boolean True on success
     */
    public function flush()
    {
        //No flushing necessary
        return true;
    }

    /**
     * @return boolean True on success
     */
    public function close()
    {
        //No closing necessary
        return true;
    }

    /**
     * @param int $length
     * @return string|boolean False on error
     */
    public function read($length = 1)
    {
        $data   = $this->storage->get($this->path);
        $chunk  = substr($data, $this->position, $length);
        $this->position += strlen($chunk);
        return $chunk;
    }

    /**
     * @param $data
     * @param integer $length
     * @return integer|boolean Number of bytes written, false on error
     */
    public function write($data, $length = null)
    {
        $dataLength = strlen($data);
        if (!is_null($length) && ($length > $dataLength)) {
            $length = $dataLength;
        }
        $storageData    = $this->storage->get($this->path);
        if ($length) {
            $storageData    = substr_replace($storageData, $data, $this->position, $length);
            $written        = $length;
            $this->position += $written;
        } else {
            $storageData    = substr_replace($storageData, $data, $this->position);
            $written        = $dataLength;
            $this->position += $written;
        }
        $this->storage->set($this->path, $storageData);
        return $written;
    }
}