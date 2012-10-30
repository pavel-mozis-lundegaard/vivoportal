<?php
namespace Vivo\CMS\UI\Content;

use Vivo\Fake\CMS;
use Vivo\CMS\UI\Component;
use Vivo\Util\MIME;

use Zend\Http\Response;

/**
 * UI component for content file.
 */
class File extends Component implements RawComponentInterface
{

    /**
     * @var CMS
     */
    private $cms;

    /**
     * @var InputStreamInterface
     */
    private $inputStream;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param CMS $cms
     * @param Response $response
     */
    public function __construct(CMS $cms, Response $response)
    {
        $this->cms = $cms;
        $this->response = $response;
    }

    public function init()
    {
        //TODO validate mimetype
        //TODO determine resource filename
        $mimeType = $this->content->getMimeType();
        $this->response->getHeaders()
            ->addHeaderLine('Content-Type: ' . $mimeType);
        $resourceFile = 'resource' . MIME::getExt($mimeType);
        $this->inputStream = $this->cms
            ->readResource($this->content, $resourceFile);
    }

    public function view()
    {
        return $this->inputStream;
    }
}
