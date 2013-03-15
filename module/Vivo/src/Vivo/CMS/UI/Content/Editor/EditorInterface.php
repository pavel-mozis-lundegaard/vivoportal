<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\ContentContainer;

interface EditorInterface
{
    /**
     * @param \Vivo\CMS\Model\Content $content
     */
    public function setContent(Content $content);

    /**
     * Save action.
     * @param \Vivo\CMS\Model\ContentContainer $container
     */
    public function save(ContentContainer $container);

}
