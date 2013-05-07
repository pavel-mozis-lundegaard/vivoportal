<?php
namespace Vivo\IO;

/**
 * Input stream, that reads data from file or any valid resource.
 * @see fopen() for valid filenames
 */
class FileInputStream implements InputStreamInterface, CloseableInterface
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
     * @throws Exception\RuntimeException
     */
    public function __construct($filename)
    {
        $this->fp = fopen($filename, 'r');
        if (!$this->fp) {
            throw new Exception\RuntimeException("Can not create stream for '$filename'");
        }
    }

    /**
     * Reads data from stream.
     * Returns null when data could not be read
     * @param integer $bytes
     * @return string|bool
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function read($bytes = 1)
    {
        if (!is_int($bytes) || $bytes < 1) {
            throw new Exception\InvalidArgumentException(
                'Parameter $bytes must be integer.');
        }
        if ($this->isClosed()) {
            throw new Exception\RuntimeException('Can not read from closed stream.');
        }
        $data = fread($this->fp, $bytes);
        if ($data == '') {
            $data = false;
        }
        return $data;
    }

    /**
     * Closes stream.
     */
    public function close()
    {
        if (!$this->isClosed()) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    /**
     * @return boolean
     */
    public function isClosed()
    {
        return $this->closed;
    }
}
