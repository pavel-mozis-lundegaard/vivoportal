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
use Vivo\Stdlib\OrderableInterface;

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
     * Constructor.
     *
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
     * Returns all fileboard items.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard $model
     * @throws \Vivo\CMS\Api\Exception\InvalidPathException
     * @return array
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
                          ->find($condition, array('sort'=>array('\order')))
                          ->getHits();

        foreach ($hits as $hit) {
            $path     = $hit->getDocument()->getFieldValue('\path');
            $return[] = $this->cmsApi->getEntity($path);
        }

        return $return;
    }

    /**
     * Prepare fileboard content before saving.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard $fileboard
     * @param \Vivo\CMS\Model\Entity $media
     * @return \Vivo\CMS\Model\Entity
     */
    private function prepareMediaForSaving(Content\Fileboard $fileboard, Entity $media)
    {
        $qb = new QueryBuilder();
        $condition = $qb->cond($fileboard->getPath().'/*', '\path');
        $hits = $this->indexer->find($condition)->getHits();

        $i = -1;
        foreach ($hits as $hit) {
            $path = $hit->getDocument()->getField('\path')->getValue();
            $parts = $this->pathBuilder->getStoragePathComponents($path);
            $lastI = $parts[count($parts) - 1];
            $i = max($i, $lastI);
        }

        $path = $this->pathBuilder->buildStoragePath(array($fileboard->getPath(), ++$i));
        $media->setPath($path);

        return $media;
    }

    /**
     * Returns CMS entity by UUID.
     *
     * @param string $ident
     * @return \Vivo\CMS\Model\Content\Fileboard\Media
     */
    public function getEntity($ident)
    {
        return $this->cmsApi->getEntity($ident);
    }

    /**
     * Removes entity.
     *
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function removeEntity(Entity $entity)
    {
        $this->cmsApi->removeEntity($entity);
    }

    /**
     * Saves entity.
     *
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function saveEntity(Entity $entity)
    {
        $this->cmsApi->saveEntity($entity, true);
    }

    /**
     * Saves fileboard separator.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard\Separator $separator
     * @param string $html HTML content
     */
    public function saveSeparator(Separator $separator, $html)
    {
        $separator->setMimeType('text/html');
        $separator->setExt('html');
        $separator->setSize(mb_strlen($html, 'UTF-8'));

        $this->fileApi->saveResource($separator, $html);
        $this->cmsApi->saveEntity($separator, true);
    }

    /**
     * Creates fileboard media file.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard $fileboard
     * @param array $file Array with uploaded file informations ($_FILE)
     * @param array $options {name, description, order}
     * @return \Vivo\CMS\Model\Content\Fileboard\Media
     */
    public function createMediaWithUploadedFile(Content\Fileboard $fileboard, array $file, array $options)
    {
        $stream = new FileInputStream($file['tmp_name']);

        $media = new Media();
        $media->setOrder($options['order']);
        $media->setName($options['name']);
        $media->setDescription($options['description']);
        $media = $this->fileApi->prepareFileForSaving($media, $file);
        $media = $this->prepareMediaForSaving($fileboard, $media);

        $this->cmsApi->saveEntity($media, true);
        $this->fileApi->writeResource($media, $stream);

        return $media;
    }

    /**
     * Creates fileboard separator.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard $fileboard
     * @param string $html HTML content.
     * @param array $options {order}
     * @return \Vivo\CMS\Model\Content\Fileboard\Separator
     */
    public function createSeparator(Content\Fileboard $fileboard, $html, array $options)
    {
        $separator = new Separator();
        $separator->setOrder($options['order']);
        $separator->setMimeType('text/html');
        $separator->setExt('html');
        $separator->setSize(mb_strlen($html, 'UTF-8'));
        $separator = $this->prepareMediaForSaving($fileboard, $separator);

        $this->fileApi->saveResource($separator, $html);
        $this->cmsApi->saveEntity($separator, true);

        return $separator;
    }

    /**
     * Swap order.
     *
     * @param \Vivo\Stdlib\OrderableInterface $entity1
     * @param \Vivo\Stdlib\OrderableInterface $entity2
     */
    public function swap(OrderableInterface $entity1, OrderableInterface $entity2)
    {
        $tmpOrder = $entity1->getOrder();
        $entity1->setOrder($entity2->getOrder());
        $entity2->setOrder($tmpOrder);

        $this->saveEntity($entity1);
        $this->saveEntity($entity2);
    }

    /**
     * Sends HTTP headers and print file content.
     *
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function download(Media $media)
    {
        $this->fileApi->download($media);
    }

    /**
     * Sends HTTP headers and print file content.
     *
     * @param string $uuid
     */
    public function downloadByUuid($uuid)
    {
        $media = $this->cmsApi->getEntity($uuid);
        $this->download($media);
    }

    /**
     * Returns content of entity resource.
     *
     * @param \Vivo\CMS\Model\Content\File $file
     * @return string
     */
    public function getResource(Content\File $file)
    {
        return $this->fileApi->getResource($file);
    }

    /**
     * Returns input stream for resource of entity.
     *
     * @param \Vivo\CMS\Model\Content\File $file
     * @return \Vivo\IO\InputStreamInterface
     */
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
