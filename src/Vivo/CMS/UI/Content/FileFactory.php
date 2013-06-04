<?php
namespace Vivo\CMS\UI\Content;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FileFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return File
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cmsApi             = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');
        $mime               = $serviceLocator->get('mime');
        $file = new File($cmsApi, $symRefConvertor, $mime);
        return $file;
    }
}
