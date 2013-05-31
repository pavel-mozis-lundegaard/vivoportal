<?php
namespace Vivo\CMS\Api\Content;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\ContentContainer;
use Vivo\CMS\Api\CMS;
use Vivo\Util\MIME;
use Vivo\CMS\Exception\InvalidArgumentException;
use Vivo\IO\InputStreamInterface;
use Vivo\IO\FileInputStream;

class File
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
     * @var \Vivo\Util\MIME
     */
    private $mime;

    /**
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\Util\MIME $mime
     */
    public function __construct(CMS $cms, $documentApi, MIME $mime)
    {
        $this->cmsApi = $cms;
        $this->documentApi = $documentApi;
        $this->mime = $mime;
    }

//     public function prepareFileModel(Content\File $model, $ext, $fileName = null)
//     {
//         $ext = strtolower($ext); //pathinfo($filename, PATHINFO_EXTENSION));
//         $mime = $this->mime->detectByExtension($ext);

//         if(!$mime) {
//             throw new InvalidArgumentException(sprintf("Unknown file extension for file '%s'", $filename));
//         }

//         $model->setFilename($fileName);
//         $model->setExt($ext);
//         $model->setMimeType($mime);

//         return $model;
//     }

    public function getExt($mimeType)
    {
        return $this->mime->getExt($mimeType);
    }

    public function saveFileWithUploadedFile(Content\File $file, array $data, ContentContainer $contentContainer = null)
    {
        $file->setFilename($data['name']);
        $file->setSize($data['size']);

        if($file->getUuid()) {
            $this->documentApi->saveContent($file);
        }
        else {
            $this->documentApi->createContent($contentContainer, $this->content);
        }

        $this->removeAllResources($file);
        $this->writeResource($file, new FileInputStream($data["tmp_name"]));
    }

    private function checkFileProperties(Content\File $file)
    {
        if(!$file->getExt()) {
            throw new InvalidArgumentException(sprintf("Unknown file extension for file '%s'", $file->getFilename()));
        }

        if(!$file->getMimeType()) {
            throw new InvalidArgumentException(sprintf("Unknown file mime type for file '%s'", $file->getFilename()));
        }
    }

    /**
     * @param \Vivo\CMS\Model\Content\File $file
     * @param string $data
     */
    public function saveResource(Content\File $file, $data)
    {
        $this->checkFileProperties($file);
        $this->cmsApi->saveResource($file, 'resource.'.$file->getExt(), $data);
    }

    /**
     * Writes resource file to repository.
     *
     * @param \Vivo\CMS\Model\Content\File $file
     * @param \Vivo\IO\InputStreamInterface $inputStream
     */
    public function writeResource(Content\File $file, InputStreamInterface $inputStream)
    {
        $this->checkFileProperties($file);
        $this->cmsApi->writeResource($file, 'resource.'.$file->getExt(), $inputStream);
    }

    /**
     * @param \Vivo\CMS\Model\Content\File $file
     * @return string
     */
    public function getResource(Content\File $file)
    {
        $this->checkFileProperties($file);
        return $this->cmsApi->getResource($file, 'resource.'.$file->getExt());
    }

    /**
     * Returns input stream for resource of entity.
     *
     * @param \Vivo\CMS\Model\Content\File $file
     * @param string $resourcePath
     * @return \Vivo\IO\InputStreamInterface
     */
    public function readResource(Content\File $file)
    {
        $this->checkFileProperties($file);
        return $this->cmsApi->readResource($file, 'resource.'.$file->getExt());
    }

    /**
     * Remove all resources.
     *
     * @param \Vivo\CMS\Model\Content\File $file
     */
    public function removeAllResources(Content\File $file)
    {
        $resources = $this->cmsApi->scanResources($file);
        foreach ($resources as $resource) {
            $this->cmsApi->removeResource($file, $resource);
        }
    }
}
