<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * The file is a basic content type. If a document with the content layout settings,
 * the file appears in it (an image directly on the page, other types of file download link),
 * otherwise it will always download the file directly for example from the Files folder.
 */
class File extends Model\Content
{
    /**
     * @var string
     */
    protected $mimeType;
    /**
     * @var string Original filename.
     */
    protected $filename;

    /**
     * Setting default values.
     *
     * @param string $path Entity path.
     */
    public function __construct($path = null)
    {
        parent::__construct($path);
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }
    /**
     * Returns file mimetype.
     *
     * @return string
     */
    public function getMimeType() {
    	return $this->mimeType;
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
}
