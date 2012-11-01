<?php
namespace Vivo\IO;

/**
 *
 */
interface InputStreamInterface
{

    /**
     * Reads from stream
     * Returns the data read or false when data cannot be read
     * @param integer $bytes Number of bytes to read
     * @return string|bool
     */
    public function read($bytes = 1);

}
