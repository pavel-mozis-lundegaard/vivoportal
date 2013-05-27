<?php
namespace Vivo\CMS\UI;

use Vivo\UI;

/**
 * This component is used to substitude an unpublished document in the component tree.
 */
class UnpublishedDocument extends UI\Text
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('<!-- Unpublished document -->');
    }
}

