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
class File extends UI\File implements RawComponentInterface, InjectModelInterface
{

    /**
     * @var CMS
     */
    private $cms;

    /**
     * @var Document
     */
    private $document;

    /**
     * @var Content
     */
    private $content;

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
        if (!$this->content instanceof Content\File) {
            throw new \Exception ("Incompatible model.");
        }

        $mimeType = $this->content->getMimeType();
        $resourceFile = 'resource' . MIME::getExt($mimeType);
        $this
            ->setInputStream(
                $this->cms->readResource($this->content, $resourceFile));

        $this->setFilename($this->content->getFilename());
    }

    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function setDocument(Document $document)
    {
        $this->document = $document;
    }
}
