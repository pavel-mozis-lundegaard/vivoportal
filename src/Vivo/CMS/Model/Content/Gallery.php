<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

class Gallery extends Model\Content
{
    /**
     * @var string Thumbnail size.
     * @example 120x120
     */
    public $imageThumbnailSize;
    /**
     * @var string Preview image size.
     * @example 640x640
     */
    public $imagePreviewSize;

    /**
     * @return string
     */
    public function getImageThumbnailSize()
    {
        return $this->imagePreviewSize;
    }

    /**
     * @param string $size
     * @example 120x120
     */
    public function setImageThumbnailSize($size)
    {
        $this->imagePreviewSize = $size;
    }

    /**
     * @return string
     */
    public function getImagePreviewSize()
    {
        return $this->imagePreviewSize;
    }

    /**
     * @param string $size
     * @example 640x640
     */
    public function setImagePreviewSize($size)
    {
        $this->imagePreviewSize = $size;
    }
}
