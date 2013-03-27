<?php
namespace Vivo\Http;

use Vivo\IO\ByteArrayInputStream;
use Vivo\IO\FileOutputStream;
use Vivo\IO\InputStreamInterface;
use Vivo\IO\OutputStreamInterface;

use Zend\Http\Response as ZendHttpResponse;

/**
 * Response object that supports setting stream as content.
 */
class StreamResponse extends ZendHttpResponse
{

    /**
     * @var InputStreamInterface
     */
    private $inputStream;

    /**
     * @var OutputStreamInterface
     */
    private $outputStream;

    /**
     * @return InputStreamInterface
     */
    public function getInputStream() {
        if ($this->inputStream == null) {
            $this->inputStream = new ByteArrayInputStream($this->content);
        }

        return $this->inputStream;
    }

    /**
     * @param InputStreamInterface $inputStream
     */
    public function setInputStream(InputStreamInterface $inputStream) {
        $this->inputStream = $inputStream;
    }

    /**
     * @return OutputStreamInterface
     */
    public function getOutputStream() {
        if ($this->outputStream == null) {
            $this->outputStream = new FileOutputStream('php://output');
        }
        return $this->outputStream;
    }

    /**
     * @param OutputStreamInterface $outputStream
     */
    public function setOutputStream(OutputStreamInterface $outputStream) {
        $this->outputStream = $outputStream;
    }
}
