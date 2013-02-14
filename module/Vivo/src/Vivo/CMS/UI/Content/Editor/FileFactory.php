<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $editor = new File();

        return $editor;
    }

}
