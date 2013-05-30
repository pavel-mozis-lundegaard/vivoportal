<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;
use Vivo\Util\MIME;

/**
 * The file is a basic content type. If a document with the content layout settings,
 * the file appears in it (an image directly on the page, other types of file download link),
 * otherwise it will always download the file directly for example from the Files folder.
 */
class File extends Model\Content
{
    /**
     * @var string MIME type.
     */
    protected $mimeType;

    /**
     * @var string Original filename.
     */
    protected $filename;

    /**
     * @var string File excension.
     */
    protected $ext;

    /**
     * @var int File size in bytes.
     */
    protected $size;

    /**
     * Sets the original file name.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        $this->ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->mimeType = MIME::getType($this->ext);
    }

    /**
     * Retuns the original file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns file mimetype.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Returns resource extension.
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Sets the size of the file in bytes.
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}
