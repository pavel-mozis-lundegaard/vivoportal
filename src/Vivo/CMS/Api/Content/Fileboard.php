<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Api;
use Vivo\CMS\Model\Entity;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Content\Fileboard\Media;
use Vivo\CMS\Model\Content\Fileboard\Separator;
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
    public function getList(Content\Fileboard $model)
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

    private function prepareMediaForSaving(Content\Fileboard $model, $media)
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
    public function getEntity($ident)
    {
        return $this->cmsApi->getEntity($ident);
    }

    /**
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function removeEntity(Entity $entity)
    {
        $this->cmsApi->removeEntity($entity);
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
     * @return \Vivo\CMS\Model\Content\Fileboard\Media
     */
    public function createMediaWithUploadedFile(Content\Fileboard $fileboard, array $file, $name, $description = null)
    {
        $stream = new FileInputStream($file['tmp_name']);

        $media = new Media();
        $media->setName($name);
        $media->setDescription($description);
        $media = $this->fileApi->prepareFileForSaving($media, $file);
        $media = $this->prepareMediaForSaving($fileboard, $media);

        $this->cmsApi->saveEntity($media, true);
        $this->fileApi->writeResource($media, $stream);

        return $media;
    }

    public function saveSeparator(Separator $separator, $html = null)
    {
        if($html) {
            $separator->setMimeType('text/html');
            $separator->setExt('html');
            $separator->setSize(mb_strlen($html, 'UTF-8'));

            $this->fileApi->saveResource($separator, $html);
            $this->cmsApi->saveEntity($separator, true);
        }
        else {
            $separator->setMimeType(null);
            $separator->setExt(null);
            $separator->setSize(0);

            $this->fileApi->removeResource($separator);
            $this->cmsApi->saveEntity($separator, true);
        }
    }

    public function createSeparator(Content\Fileboard $fileboard, $html)
    {
        $separator = new Separator();
        $separator->setMimeType('text/html');
        $separator->setExt('html');
        $separator->setSize(mb_strlen($html, 'UTF-8'));
        $separator = $this->prepareMediaForSaving($fileboard, $separator);

        $this->fileApi->saveResource($separator, $html);
        $this->cmsApi->saveEntity($separator, true);

        return $separator;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function download(Media $media)
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

    public function getResource(Content\File $file)
    {
        return $this->fileApi->getResource($file);
    }

    public function readResource(Content\File $file)
    {
        return $this->fileApi->readResource($file);
    }

    /**
     * Removes all fileboard's content.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard $fileboard
     */
    public function removeAllFiles(Content\Fileboard $fileboard)
    {
        foreach ($this->cmsApi->getChildren($fileboard) as $child) {
            $this->cmsApi->removeEntity($child);
        }
    }
}
