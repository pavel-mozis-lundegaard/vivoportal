<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\UI\AbstractForm;
use \Vivo\CMS\Model\Content;
use Vivo\CMS\UI\Content\Editor\AdapterInterface;

/**
 * Abstract class for Resource Editor Adapters
 */
abstract class AbstractAdapter extends AbstractForm implements AdapterInterface
{

    /**
     * Content
     * @var Content
     */
    protected $content;

    /**
     * When set to true, CSRF protection will be automatically added to the form
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoAddCsrf          = false;

    /**
     * When set to true, data will be automatically loaded to the form from request
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoLoadFromRequest  = false;

    /**
     * Sets Content
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }
}
