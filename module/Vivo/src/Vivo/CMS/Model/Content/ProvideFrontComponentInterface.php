<?php
namespace Vivo\CMS\Model\Content;

/**
 * Interface for contents that could have their own front component.
 */
interface ProvideFrontComponentInterface
{
    /**
     * Returns FQCN of front component.
     */
    public function getFrontComponent();
}
