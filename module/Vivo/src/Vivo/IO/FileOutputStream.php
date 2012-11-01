<?php
namespace Vivo\IO;

use Vivo\IO\Exception\RuntimeException;

/**
 *
 */
class FileOutputStream implements OutputStreamInterface, CloseableInterface
{

    /**
     * @var resource
     */
    private $fp;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var boolean
     */
    private $closed;

    /**
     * @param string $path
     * @param boolean $append
     */
    public function __construct($filename, $append = false)
    {
        $mode = $append ? 'a' : 'w';
        $this->fp = fopen($filename, $mode);
        if (!$this->fp) {
            throw new RuntimeException("Can not create stream for '$filename'");
        }
    }

    /**
     * Writes data to the stream.
     * @param string $data
     * @return integer
     */
    public function write($data)
    {
        if ($this->isClosed()) {
            throw new RuntimeException('Can not write to closed stream.');
        }
        return fwrite($this->fp, $data);
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
