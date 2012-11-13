<?php
namespace Vivo\ZendSearch\Lucene\Storage\File;

use Vivo\Storage\FileHandle;
use ZendSearch\Lucene;

class StorageHandle extends AbstractFile
{
    /**
     * Resource of the open file
     * @var FileHandle
     */
    protected $_fileHandle;

    /**
     * Constructor
     * @param \Vivo\Storage\FileHandle $fileHandle
     */
    public function __construct(FileHandle $fileHandle)
    {
        $this->_fileHandle  = $fileHandle;
    }

    /**
     * Sets the file position indicator and advances the file pointer.
     * The new position, measured in bytes from the beginning of the file,
     * is obtained by adding offset to the position specified by whence,
     * whose values are defined as follows:
     * SEEK_SET - Set position equal to offset bytes.
     * SEEK_CUR - Set position to current location plus offset.
     * SEEK_END - Set position to end-of-file plus offset. (To move to
     * a position before the end-of-file, you need to pass a negative value
     * in offset.)
     * SEEK_CUR is the only supported offset type for compound files
     *
     * Upon success, returns 0; otherwise, returns -1
     *
     * @param integer $offset
     * @param integer $whence
     * @return integer
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->_fileHandle->seek($offset, $whence);
    }


    /**
     * Get file position.
     *
     * @return integer
     */
    public function tell()
    {
        return $this->_fileHandle->tell();
    }

    /**
     * Flush output.
     *
     * Returns true on success or false on failure.
     *
     * @return boolean
     */
    public function flush()
    {
        return $this->_fileHandle->flush();
    }

    /**
     * Close File object
     */
    public function close()
    {
        if ($this->_fileHandle !== null ) {
            $this->_fileHandle->close();
            $this->_fileHandle = null;
        }
    }

    /**
     * Get the size of the already opened file
     *
     * @return integer
     */
    public function size()
    {
        $position   = $this->_fileHandle->tell();
        $this->_fileHandle->seek(0, SEEK_END);
        $size       = $this->_fileHandle->tell();
        $this->_fileHandle->seek($position, SEEK_SET);
        return $size;
    }

    /**
     * Read a $length bytes from the file and advance the file pointer.
     * @param integer $length
     * @return string
     */
    protected function _fread($length = 1)
    {
        if ($length == 0) {
            return '';
        }
        if ($length < 1024) {
            return $this->_fileHandle->read($length);
        }
        $data = '';
        while ($length > 0 && ($nextBlock = $this->_fileHandle->read($length)) != false) {
            $data .= $nextBlock;
            $length -= strlen($nextBlock);
        }
        return $data;
    }

    /**
     * Writes $length number of bytes (all, if $length===null) to the end
     * of the file.
     *
     * @param string $data
     * @param integer $length
     */
    protected function _fwrite($data, $length = null)
    {
        if ($length === null ) {
            $this->_fileHandle->write($data);
        } else {
            $this->_fileHandle->write($data, $length);
        }
    }

    /**
     * Lock file
     *
     * Lock type may be a LOCK_SH (shared lock) or a LOCK_EX (exclusive lock)
     *
     * @param integer $lockType
     * @param boolean $nonBlockingLock
     * @return boolean
     */
    public function lock($lockType, $nonBlockingLock = false)
    {
        //TODO - Locking not supported
        return true;

//        if ($nonBlockingLock) {
//            return flock($this->_fileHandle, $lockType | LOCK_NB);
//        } else {
//            return flock($this->_fileHandle, $lockType);
//        }
    }

    /**
     * Unlock file
     *
     * Returns true on success
     *
     * @return boolean
     */
    public function unlock()
    {
        //TODO - Locking not supported
        return true;

//        if ($this->_fileHandle !== null ) {
//            return flock($this->_fileHandle, LOCK_UN);
//        } else {
//            return true;
//        }
    }
}
