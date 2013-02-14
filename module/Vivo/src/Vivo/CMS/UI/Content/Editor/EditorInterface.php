<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;

interface EditorInterface
{
    /**
     * @param Content $content
     */
    public function setContent(Content $content);

    /**
     * Save action.
     */
    public function save();

}
