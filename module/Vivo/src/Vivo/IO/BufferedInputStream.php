<?php
namespace Vivo\IO;

use Vivo\IO\Exception\InvalidArgumentException;
use Vivo\IO\Exception\RuntimeException;
use Vivo\IO\InputStreamInterface;

/**
 *
 */
class BufferedInputStream implements InputStreamInterface, CloseableInterface
{

    /**
     * @var integer
     */
    private $position = 0;

    /**
     * @var \Vivo\IO\InputStreamInterface
     */

    private $is;

    /**
     * @var integer
     */
    private $bufferSize;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @var boolean
     */
    private $closed = false;

    /**
     *
     * @param string $data
     */
    public function __construct(InputStreamInterface $is, $bufferSize = 1024)
    {
        $this->is = $is;
        $this->bufferSize = $bufferSize;
    }

    /**
     * @param int $bytes
     * @return string
     * @throws RuntimeException
     * @throws InvalidArgumentException
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

        while (strlen($this->buffer) < $bytes && $this->loadBuffer())
            ;
        $data = substr($this->buffer, 0, $bytes);
        $this->buffer = substr($this->buffer, $bytes);
        return $data ? : false;
    }

    /**
     * @return boolean
     */
    private function loadBuffer()
    {
        $data = $this->is->read($this->bufferSize);
        if ($data === false) {
            return false;
        }
        $this->buffer .= $data;
        return true;
    }

    /**
     * Closes stream
     */
    public function close()
    {
        $this->buffer = ''; //free memory
        if ($this->is instanceof CloseableInterface) {
            $this->is->close();
        }
        $this->closed = true;
    }

    /**
     * @return boolean
     */
    public function isClosed()
    {
        return $this->closed;
    }
}
