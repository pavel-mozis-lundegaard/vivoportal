<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Api;
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

    /**
     * @var \Vivo\Storage\PathBuilder\PathBuilderInterface
     */
    private $pathBuilder;

    /**
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\Content\File $fileApi
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     */
    public function __construct(
        Api\CMS $cmsApi,
        Api\Content\File $fileApi,
        \Vivo\Indexer\IndexerInterface $indexer,
        \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder)
    {
        $this->cmsApi = $cmsApi;
        $this->fileApi = $fileApi;
        $this->indexer = $indexer;
        $this->pathBuilder = $pathBuilder;
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
        $hits      = $this->indexer
                          ->find($condition, array('sort'=>array('\Vivo\CMS\Model\Content\Fileboard\Media\order')))
                          ->getHits();

        foreach ($hits as $hit) {
            $path     = $hit->getDocument()->getFieldValue('\path');
            $return[] = $this->cmsApi->getEntity($path);
        }

        return $return;
    }

    private function prepareMediaForSaving(Content\Fileboard $model, Media $media)
    {
        $qb = new QueryBuilder();
        $condition = $qb->cond($model->getPath().'/*', '\path');
        $hits = $this->indexer->find($condition)->getHits();

        $i = -1;
        foreach ($hits as $hit) {
            $path = $hit->getDocument()->getField('\path')->getValue();
            $parts = $this->pathBuilder->getStoragePathComponents($path);
            $lastI = $parts[count($parts) - 1];
            $i = max($i, $lastI);
        }

        $path = $this->pathBuilder->buildStoragePath(array($model->getPath(), ++$i));
        $media->setPath($path);

        return $media;
    }

    /**
     * @param string $ident
     * @return \Vivo\CMS\Model\Content\Fileboard\Media
     */
    public function getMedia($ident)
    {
        return $this->cmsApi->getEntity($ident);
    }

    public function removeMedia(Media $media)
    {
        $this->cmsApi->removeEntity($media);
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function saveMedia(Media $media)
    {
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
        $stream = new FileInputStream($file['tmp_name']);

        $media = new Content\Fileboard\Media();
        $media->setName($name);
        $media->setDescription($description);
        $media = $this->fileApi->prepareFileForSaving($media, $file);
        $media = $this->prepareMediaForSaving($fileboard, $media);

        $this->cmsApi->saveEntity($media, true);
        $this->fileApi->writeResource($media, $stream);
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
