<?php
namespace Vivo\Module;

use Vivo\IO\FileInputStream;

/**
 * ResourceProviderInterface
 * Modules implementing this interface control access to their resources themselves
 */
interface ResourceProviderInterface
{
    /**
     * Returns the resource data
     * @param string $type
     * @param string $pathToResource
     * @return string
     */
    public function getResource($type, $pathToResource);

    /**
     * Returns an input stream for the resource
     * @param string $type
     * @param string $pathToResource
     * @return FileInputStream
     */
    public function getResourceStream($type, $pathToResource);
}