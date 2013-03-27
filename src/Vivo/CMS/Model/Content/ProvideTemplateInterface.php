<?php
namespace Vivo\CMS\Model\Content;

/**
 * Interface for content that could define own template.
 */
interface ProvideTemplateInterface
{
    /**
     * Returns template key.
     * @return string Key in template map.
     */
    public function getTemplate();
}
