<?php
namespace Vivo\Backend\UI\Explorer;

/**
 * Ribbon for explorer.
 *
 */
class Ribbon extends \Vivo\UI\Ribbon
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setViewAll(true);
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::getDefaultTemplate()
     */
    public function getDefaultTemplate()
    {
        //use parent template
        return get_parent_class($this);
    }
}
