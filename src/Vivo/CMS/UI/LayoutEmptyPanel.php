<?php
namespace Vivo\CMS\UI;

use Vivo\UI;

/**
 * This component is used to substitude an empty layout panel in the component tree.
 */
class LayoutEmptyPanel extends UI\Text
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('<!-- Layout empty panel -->');
    }
}

