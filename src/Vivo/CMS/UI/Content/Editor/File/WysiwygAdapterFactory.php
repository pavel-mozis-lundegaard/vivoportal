<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WysiwygAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = new WysiwygAdapter();
        return $adapter;
    }

}
