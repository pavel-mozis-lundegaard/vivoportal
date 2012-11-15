<?php
namespace Vivo\IO\Filter;

use Vivo\IO\InputStreamInterface;

class ImageTransforation implements InputStreamInterface
{

    private $inputStream;

    private $options = array();

    private $transformed = false;

    public function __construct(InputStreamInterface $inputStream, $options = array()) {
        $this->inputStream = $inputStream;
        $this->options = $options;
    }
    public function read($bytes = 1)
    {
        if (!$this->transformed) $this->transformImage();
        return $this->inputStream->read($bytes);
    }

    protected function transformImage() {
        //TODO
        $this->transformed = true;
    }

}
