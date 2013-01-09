<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Content;
use Vivo\CMS\UI\Component;
use Vivo\CMS\UI\Exception;
use Vivo\Util\MIME;

/**
 * UI component for content file.
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
     */
    public function __construct(CMS $cms)
    {
        $this->cms = $cms;
    }

    public function init()
    {
        //TODO validate mimetype
        //TODO determine resource filename and mimetype
        //TODO include phtml resource, tpl resource

        if (!$this->content instanceof Content\File) {
            throw new Exception\Exception (sprintf("%s: Incompatible model. Expected 'Vivo\CMS\Model\Content\File'.", __METHOD__));
        }

        $mimeType = $this->content->getMimeType();
        $resourceFile = 'resource.' . MIME::getExt($mimeType);
        $this->view->resourceFile = $resourceFile;
        if ($mimeType == 'text/html') {
            $this->view->fileContent = $this->cms->getResource($this->content, $resourceFile);
            $templateVariant = 'html';
        } elseif ($mimeType == 'text/plain') {
            $this->view->fileContent = $this->cms->getResource($this->content, $resourceFile);
            $templateVariant = 'plain';
        } elseif ($mimeType == 'application/x-shockwave-flash') {
            $templateVariant = 'flash';
        } elseif (substr($mimeType, 0, 6) == 'image/') {
            $templateVariant = 'image';
        } else {
            $templateVariant = null;
        }
        $this->view->setTemplate($this->getDefaultTemplate() . ':' . $templateVariant);
    }
}
