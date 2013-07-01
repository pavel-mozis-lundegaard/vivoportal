<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Content\Gallery\Media;
use Vivo\Indexer\QueryBuilder;
use Vivo\IO\FileInputStream;
use Vivo\CMS\Api\Exception\InvalidPathException;

class Gallery extends AbstractOrderableContentApi
{
    /**
     * @param \Vivo\CMS\Model\Content\Gallery $model
     * @throws \Vivo\CMS\Api\Exception\InvalidPathException
     * @return array
     */
    public function getList(Content\Gallery $model)
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
     * Creates fileboard media file.
     *
     * @param \Vivo\CMS\Model\Content\Gallery $gallery
     * @param array $file Array with uploaded file informations ($_FILE)
     * @param array $options {name, description, order}
     * @return \Vivo\CMS\Model\Content\Fileboard\Media
     */
    public function createMediaWithUploadedFile(Content\Gallery $gallery, array $file, array $options)
    {
        $stream = new FileInputStream($file['tmp_name']);

        $media = new Media();
        $media->setOrder($options['order']);
        $media->setName($options['name']);
        $media->setDescription($options['description']);
        $media = $this->fileApi->prepareFileForSaving($media, $file);
        $media = $this->prepareMediaForSaving($gallery, $media);

        $this->cmsApi->saveEntity($media, true);
        $this->fileApi->writeResource($media, $stream);

        return $media;
    }

}
