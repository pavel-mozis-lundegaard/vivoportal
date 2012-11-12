<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\CMS;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\CMS\UI\Component;
use Vivo\CMS\UI\InjectModelInterface;
use Vivo\UI;
use Vivo\Util\MIME;

use Zend\Http\Headers;
use Zend\Http\Response;

/**
 * UI component for content file.
 * @todo add logic for various file types, when the file is not main content of page
 */
class File extends Component
{

    /**
     * @var CMS
     */
    private $cms;

    /**
     * @var string
     */
    private $templateVariant;

    /**
     * @param CMS $cms
     * @param Response $response
     */
    public function __construct(CMS $cms, Response $response)
    {
        parent::__construct($response);
        $this->cms = $cms;
    }

    public function init()
    {
        //TODO validate mimetype
        //TODO determine resource filename and mimetype
        //TODO include phtml resource, tpl resource

        if (!$this->content instanceof Content\File) {
            throw new \Exception ("Incompatible model.");
        }

        $mimeType = $this->content->getMimeType();
        $resourceFile = 'resource' . MIME::getExt($mimeType);
        if ($mimeType == 'text/html') {
            $this->view->content = $this->cms->getResource($this->content, $resourceFile);
            $this->templateVariant = 'html';
        } elseif ($mimeType == 'text/plain') {
            $this->view->content = $this->cms->getResource($this->content, $resourceFile);
            $this->templateVariant = 'plain';
        } elseif ($mimeType == 'application/x-shockwave-flash') {
            $this->templateVariant = 'flash';
        } else {
            $this->templateVariant = null;
        }
    }

    public function getDefaultTemplate() {
        return parent::getDefaultTemplate() . ($this->templateVariant ? ':'. $this->templateVariant :'');
    }
}
