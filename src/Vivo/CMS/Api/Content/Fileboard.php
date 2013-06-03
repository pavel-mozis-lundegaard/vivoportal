<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Content\Fileboard\Media;
use Vivo\Indexer\QueryBuilder;
use Vivo\IO\FileInputStream;
use Vivo\CMS\Api\Exception\InvalidPathException;

class Fileboard
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    /**
     * @var \Vivo\CMS\Api\Content\File
     */
    private $fileApi;

    /**
     * @var \Vivo\Indexer\IndexerInterface
     */
    private $indexer;

    public function __construct($cmsApi, $fileApi, $pathBuilder, $indexer)
    {
        $this->cmsApi = $cmsApi;
        $this->fileApi = $fileApi;
        $this->pathBuilder = $pathBuilder;
        $this->indexer = $indexer;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard $model
     * @throws \Vivo\CMS\Api\Exception\InvalidPathException
     * @return array <\Vivo\CMS\Model\Content\Fileboard\Media>
     */
    public function getMediaList(Content\Fileboard $model)
    {
        $return = array();

        if(!$model->getPath()) {
            throw new InvalidPathException('Entity path is not set');
        }

        $qb = new QueryBuilder();
        $condition = $qb->cond($model->getPath().'/*', '\path');
        $hits      = $this->indexer->find($condition)->getHits();

        foreach ($hits as $hit) {
            $path     = $hit->getDocument()->getFieldValue('\path');
            $return[] = $this->cmsApi->getEntity($path);
        }

        //TODO: move to query
        usort($return, function($a, $b) { /* @var $a \Vivo\CMS\Model\Content\Fileboard\Media */
            return $a->getOrder() > $b->getOrder();
        });

        return $return;
    }

    private function prepareMediaForSaving(Content\Fileboard $model, Media $media)
    {
        $qb = new QueryBuilder();
        $condition = $qb->cond($model->getPath().'/*', '\path');
        $count     = $this->indexer->find($condition, array('page_size'=>0))->getTotalHitCount();

        $path = $this->pathBuilder->buildStoragePath(array($model->getPath(), $count));
        $media->setPath($path);

        return $media;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard $model
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    private function saveMedia(Content\Fileboard $model, Media $media)
    {
        $media = $this->prepareMediaForSaving($model, $media);

        $this->cmsApi->saveEntity($media, true);
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard $fileboard
     * @param array $file
     * @param string $name
     * @param string $description
     */
    public function saveMediaWithUploadedFile(Content\Fileboard $fileboard, array $file, $name, $description = null)
    {
        $media = new Content\Fileboard\Media();
        $media->setName($name);
        $media->setDescription($description);

        $media = $this->fileApi->prepareFileForSaving($media, $file);

        $this->saveMedia($fileboard, $media);
        $this->fileApi->writeResource($media, new FileInputStream($file['tmp_name']));
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function download(Content\Fileboard\Media $media)
    {
        $this->fileApi->download($media);
    }

    /**
     * @param string $uuid
     */
    public function downloadByUuid($uuid)
    {
        $media = $this->cmsApi->getEntity($uuid);
        $this->download($media);
    }

    public function removeAllFiles(Content\Fileboard $fileboard)
    {

    }
}
