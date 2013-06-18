<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Api;
use Vivo\CMS\Model\Entity;
use Vivo\CMS\Model\Content;
use Vivo\Indexer\QueryBuilder;
use Vivo\IO\FileInputStream;
use Vivo\CMS\Api\Exception\InvalidPathException;
use Vivo\Stdlib\OrderableInterface;

class Gallery
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
     * @param \Vivo\CMS\Model\Content\Gallery $model
     * @throws \Vivo\CMS\Api\Exception\InvalidPathException
     * @return array
     */
    public function getList(Content\Gallery $model)
    {
        $return = array();

        return $return;
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
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function saveEntity(Entity $entity)
    {
        $this->cmsApi->saveEntity($entity, true);
    }

    /**
     * Swap order.
     *
     * @param \Vivo\Stdlib\OrderableInterface $entity1
     * @param \Vivo\Stdlib\OrderableInterface $entity2
     */
    public function swap(OrderableInterface $entity1, OrderableInterface $entity2)
    {

    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function download(Media $media)
    {
        $this->fileApi->download($media);
    }

    /**
     * @param \Vivo\CMS\Model\Content\File $file
     * @return string
     */
    public function getResource(Content\File $file)
    {
        return $this->fileApi->getResource($file);
    }

    /**
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
