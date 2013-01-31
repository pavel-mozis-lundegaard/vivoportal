<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;

/**
 * Markup Interface for UI component which needs related document and content.
  */
interface InjectModelInterface
{
    /**
     * Sets content
     * @param Content $content
     * @return void
     */
    public function setContent(Content $content);

    /**
     * Sets document
     * @param Document $document
     * @return void
     */
    public function setDocument(Document $document);
}
