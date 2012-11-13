<?php
namespace Vivo\ZendSearch\Lucene\Storage\File;

use ZendSearch\Lucene\Storage\File\AbstractFile as ZendSearchAbstractFile;

/**
 * AbstractFile
 * Abstract Lucene file
 * Adds abstract methods missing in the ZendSearch\Lucene\Storage\File\AbstractFile
 */
abstract class AbstractFile extends ZendSearchAbstractFile
{
    /**
     * Close the file object
     * @return void
     */
    abstract public function close();

    /**
     * Returns size of the file
     * @return integer
     */
    abstract public function size();

    /**
     * Read a $length bytes from the file and advance the file pointer.
     * @param integer $length
     * @return string
     */
    abstract protected function _fread($length = 1);

    /**
     * Writes $length number of bytes (all, if $length===null) to the end
     * of the file.
     * @param string $data
     * @param integer|null $length
     * @return
     */
    abstract protected function _fwrite($data, $length = null);
}