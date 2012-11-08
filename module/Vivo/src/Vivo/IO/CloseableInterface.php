<?php
namespace Vivo\IO;

interface CloseableInterface
{
    /**
     * Closes the resource
     * @return mixed
     */
    public function close();

    /**
     * Returns if the resource is closed
     * @return boolean
     */
    public function isClosed();
}
