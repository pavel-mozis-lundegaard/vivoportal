<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Content;
use Vivo\CMS\UI\Component;
use Vivo\CMS\UI\Exception;
use Vivo\Util\MIME;
use Vivo\CMS\RefInt\SymRefConvertorInterface;

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
     * Symbolic reference convertor
     * @var SymRefConvertorInterface
     */
    protected $symRefConvertor;

    /**
     * Constructor
     * @param CMS $cms
     * @param \Vivo\CMS\RefInt\SymRefConvertorInterface $symRefConvertor
     */
    public function __construct(CMS $cms, SymRefConvertorInterface $symRefConvertor)
    {
        $this->cms              = $cms;
        $this->symRefConvertor  = $symRefConvertor;
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
            $fileContent                = $this->cms->getResource($this->content, $resourceFile);
            $this->view->fileContent    = $this->symRefConvertor->convertReferencesToURLs($fileContent);
            $templateVariant = 'html';
        } elseif ($mimeType == 'text/plain') {
            $fileContent                = $this->cms->getResource($this->content, $resourceFile);
            $this->view->fileContent    = $this->symRefConvertor->convertReferencesToURLs($fileContent);
            $templateVariant = 'plain';
        } elseif ($mimeType == 'application/x-shockwave-flash') {
            $templateVariant = 'flash';
        } elseif (substr($mimeType, 0, 6) == 'image/') {
            $templateVariant = 'image';
        } else {
            $templateVariant = null;
        }
        $this->view->setTemplate($this->getDefaultTemplate() .
                ($templateVariant ? ':' . $templateVariant : ''));
    }
}
