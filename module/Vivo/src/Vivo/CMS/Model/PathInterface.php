<?php
namespace Vivo\CMS\Model;

/**
 * PathInterface
 */
interface PathInterface
{
    /**
     * Returns object's path
     * @return string
     */
    public function getPath();

    /**
     * Sets object's path
     * @param string $path
     * @return void
     */
    public function setPath($path);
}
