<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Content\Fileboard\Media;

class Fileboard
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    public function __construct($cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard $model
     * @return array <\Vivo\CMS\Model\Content\Fileboard\Media>
     */
    public function getMediaList(Content\Fileboard $model)
    {
        /* @var $model \Vivo\CMS\Model\Content\Fileboard */
        $array = CMS::$repository->indexer->query('vivo_cms_model_entity_path:'.$model->getPath().'/*',
                array('sort' => 'vivo_cms_model_content_fileboard_media_order_i asc', 'limit' => 9999999));

        return $array;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function addMedia(Media $media)
    {

    }

    public function removeAllFiles()
    {

    }

    public function getFileExtension($filename)
    {
        return substr($filename, strrpos($filename, '.') + 1);
    }
}
