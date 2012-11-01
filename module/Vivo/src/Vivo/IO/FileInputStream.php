<?php
namespace Vivo\IO;

use Vivo\IO\Exception\RuntimeException;
use Vivo\IO\Exception\InvalidArgumentException;

/**
 * Input stream, that reads data from file or any valid resoure.
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
     * @param string $file
     * @throws RuntimeException
     */
    public function __construct($filename)
    {
        $this->fp = fopen($filename, 'r');
        if (!$this->fp) {
            throw new RuntimeException("Can not create stream for '$filename'");
        }
    }

    /**
     * Reads data from stream.
     * Returns null when data could not be read
     * @param integer $bytes
     * @return string|bool
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function read($bytes = 1)
    {
        if (!is_int($bytes) || $bytes < 1) {
            throw new InvalidArgumentException(
                'Parameter $bytes must be integer.');
        }
        if ($this->isClosed()) {
            throw new RuntimeException('Can not read from closed stream.');
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
