<?php
namespace Vivo\IO;

/**
 * FileInOutStream
 */
class FileInOutStream implements InOutStreamInterface, CloseableInterface
{
    /**
     * @var resource
     */
    private $fp;

    /**
     * @var boolean
     */
    private $closed = false;

    /**
     * Constructor
     * @param string $filename
     * @param bool $append
     * @throws Exception\RuntimeException
     */
    public function __construct($filename, $append = false)
    {
        $mode = $append ? 'a+b' : 'w+b';
        $this->fp = fopen($filename, $mode);
        if (!$this->fp) {
            throw new Exception\RuntimeException("Can not create stream for '$filename'");
        }
    }

    /**
     * Closes the resource
     * @return void
     */
    public function close()
    {
        if (!$this->isClosed()) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    /**
     * Returns if the resource is closed
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * Reads from stream
     * Returns the data read or false when data cannot be read
     * @param integer $bytes Number of bytes to read
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     * @return string|bool
     */
    public function read($bytes = 1)
    {
        if (!is_int($bytes) || $bytes < 1) {
            throw new Exception\InvalidArgumentException(
                'Parameter $bytes must be integer.');
        }
        if ($this->isClosed()) {
            throw new Exception\RuntimeException('Cannot read from closed stream.');
        }
        $data = fread($this->fp, $bytes);
        if ($data == '') {
            $data = false;
        }
        return $data;
    }

    /**
     * Writes to stream
     * @param string $data
     * @param null $length
     * @throws Exception\RuntimeException
     * @return integer
     */
    public function write($data, $length = null)
    {
        if ($this->isClosed()) {
            throw new Exception\RuntimeException('Cannot write to closed stream.');
        }
        if (is_null($length)) {
            return fwrite($this->fp, $data);
        } else {
            return fwrite($this->fp, $data, $length);
        }
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
        return fseek($this->fp, $offset, $whence);
    }

    /**
     * Get file position.
     * @return integer
     */
    public function tell()
    {
        return ftell($this->fp);
    }

    /**
     * Flush output.
     * Returns true on success or false on failure.
     * @return boolean
     */
    public function streamFlush()
    {
        return fflush($this->fp);
    }
}