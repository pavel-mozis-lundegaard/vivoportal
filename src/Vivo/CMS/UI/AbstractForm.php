<?php
namespace Vivo\CMS\UI;

use Vivo\UI\AbstractForm as AbstractVivoForm;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;

/**
 * AbstractForm
 * Abstract CMS Form
 */
abstract class AbstractForm extends AbstractVivoForm implements InjectModelInterface
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Document
     */
    protected $document;

    /**
     * Sets content
     * @param Content $content
     * @return void
     */
    public function setContent(Content $content)
    {
        $this->content  = $content;
    }

    /**
     * Sets document
     * @param Document $document
     * @return void
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }
}
