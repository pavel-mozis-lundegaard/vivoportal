<?php
namespace Vivo\Fake;

use Vivo\IO\FileInputStream;
use Vivo\Repository\Repository;

class CMS extends \Vivo\CMS\CMS
{

    public function __construct(Repository $repository)
    {
        parent::__construct($repository);
    }

    public function readResource($entity, $resource)
    {
        return new FileInputStream(__DIR__ . '/' . $resource);
    }

    public function getResource($entity, $resourceFile)
    {
        return $data = 'sample stream content';
    }

    public function getPublishedContents($document)
    {
        return array($this->getDocumentContent($document, 1, 1));//,$this->getDocumentContent($document, 1, 1));
    }
}
