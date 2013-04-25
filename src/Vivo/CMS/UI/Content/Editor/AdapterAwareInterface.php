<?php
namespace Vivo\CMS\UI\Content\Editor;

/**
 * AdapterAwareInterface
 * Implementors accept an editor adapter
 */
interface AdapterAwareInterface
{
    /**
     * Sets the editor adapter
     * @param AdapterInterface $adapter
     * @return void
     */
    public function setAdapter(AdapterInterface $adapter = null);

    /**
     * Returns key under which an editor adapter is searched in configuration
     * @return string
     */
    public function getAdapterKey();
}
