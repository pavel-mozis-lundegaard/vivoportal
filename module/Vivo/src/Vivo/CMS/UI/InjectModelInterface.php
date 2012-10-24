<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;

/**
 * Markup Interface for UI component which needs related document and content.
  */
interface InjectModelInterface
{
    public function setContent(Content $content);
    public function setDocument(Document $document);
}
