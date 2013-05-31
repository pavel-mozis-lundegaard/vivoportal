<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fileApi            = $serviceLocator->get('Vivo\CMS\Api\Content\File');
        $docApi             = $serviceLocator->get('Vivo\CMS\Api\Document');
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');

        $editor = new File($fileApi, $docApi, $symRefConvertor);

        return $editor;
    }

}
