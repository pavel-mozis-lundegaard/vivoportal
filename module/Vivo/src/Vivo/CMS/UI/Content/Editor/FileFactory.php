<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cmsApi = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $docApi = $serviceLocator->get('Vivo\CMS\Api\Document');

        $editor = new File($cmsApi, $docApi);

        return $editor;
    }

}
