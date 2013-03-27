<?php
namespace Vivo\UI;

use Zend\Http\Response;

use Vivo\IO\InputStreamInterface;
use Vivo\Util;

use Zend\Http\Headers;

/**
 * If a document with the content layout settings, the file appears in it (an image directly on the page, other types of file download link),
 * otherwise it will always download the file directly for example from the Files folder.
 */
class File extends Component
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $disposition = 'attachment';

    /**
     * @var InputStreamInterface
     */
    private $inputStream;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $options = array('setHeaders' => true);

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param InpuStreamInterface $inputStream
     */
    public function setInputStream(InputStreamInterface $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return InputStreamInterface
     */
    function view()
    {
        if (!$this->mimeType) {
            $ext = substr($this->filename, strrpos($this->filename, '.') + 1);
            $this->mimeType = Util\MIME::getType($ext);
        }

        if ($this->options['setHeaders'] == true) {
            $this->response->getHeaders()
                ->addHeaderLine('Content-Type: ' . $this->mimeType)
                ->addHeaderLine('Content-Disposition: '.$this->getDisposition() . '; filename="'.$this->getFilename().'"');
        }

        return $this->inputStream;
    }

    /**
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getDisposition() {
        return $this->disposition;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

}

