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
     * @throws Exception\RuntimeException
     * @return int
     */
    public function write($data)
    {
        if ($this->isClosed()) {
            throw new Exception\RuntimeException('Cannot write to closed stream.');
        }
        return fwrite($this->fp, $data);
    }
}