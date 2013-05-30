<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Content\Fileboard\Media;
use Vivo\Indexer\QueryBuilder;

class Fileboard
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * @var \Vivo\Indexer\IndexerInterface
     */
    private $indexer;

    public function __construct($cmsApi, $documentApi, $pathBuilder, $indexer)
    {
        $this->cmsApi = $cmsApi;
        $this->documentApi = $documentApi;
        $this->pathBuilder = $pathBuilder;
        $this->indexer = $indexer;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard $model
     * @return array <\Vivo\CMS\Model\Content\Fileboard\Media>
     */
    public function getMediaList(Content\Fileboard $model)
    {
        $return = array();

        $qb = new QueryBuilder();
        $condition = $qb->cond($model->getPath().'/*', '\path');
        $hits      = $this->indexer->find($condition)->getHits();

        foreach ($hits as $hit) {
            $path     = $hit->getDocument()->getFieldValue('\path');
            $return[] = $this->cmsApi->getEntity($path);
        }

        usort($return, function($a, $b) { /* @var $a \Vivo\CMS\Model\Content\Fileboard\Media */
            return $a->getOrder() > $b->getOrder();
        });

        return $return;
    }

    /**
     * @param \Vivo\CMS\Model\Content\Fileboard\Media $media
     */
    public function addMedia(Content\Fileboard $model, Media $media)
    {
        $qb = new QueryBuilder();
        $condition = $qb->cond($model->getPath().'/*', '\path');
        $count     = $this->indexer->find($condition, array('page_size'=>0))->getTotalHitCount();

        $path = $this->pathBuilder->buildStoragePath(array($model->getPath(), $count));
        $media->setPath($path);

        $this->cmsApi->saveEntity($media, true);
    }

    public function uploadMedia(Content\Fileboard $model, array $file, $name, $description)
    {
        $ext = $this->getFileExtension($file['name']);
        $resourceName = 'resource.'.$ext;

        $media = new Content\Fileboard\Media();
        $media->setFilename($file['name']);
        $media->setMimeType($file['type']);
        $media->setExt($ext);
        $media->setSize(filesize($file['tmp_name']));
        $media->setName($name);
        $media->setDescription($description);

        $this->addMedia($model, $media);
        $this->cmsApi->saveResource($media, $resourceName, file_get_contents($file['tmp_name']));
    }

    public function removeAllFiles()
    {

    }

    protected function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
