<?php
namespace Vivo\IO\Filter;

use Vivo\IO\InputStreamInterface;

class UpperCase implements InputStreamInterface
{

    /**
     * @var InputStreamInterface
     */
    private $is;

    /**
     *
     * @param InputStreamInterface $inputStream
     */
    public function __construct(InputStreamInterface $inputStream)
    {
        $this->is = $inputStream;
    }

    public function read($bytes = 1) {
        $data = $this->is->read($bytes);
        if ($data === false) return false;
        return strtoupper($data);
    }
}