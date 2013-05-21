<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cmsApi             = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $docApi             = $serviceLocator->get('Vivo\CMS\Api\Document');
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');
        $editor = new File($cmsApi, $docApi, $symRefConvertor);
        $editor->setMime($serviceLocator->get('Vivo\Util\MIME'));
        return $editor;
    }

}
