<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\UI\ComponentContainer;

/**
 * Base component for CMS UI components.
 */
class Component extends ComponentContainer implements InjectModelInterface
{

    /**
     * @var \Vivo\CMS\Model\Document
     */
    protected $document;

    /**
     * @var \Vivo\CMS\Model\Content
     */
    protected $content;

    /**
     * @param Document $document
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }
}
