<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fileApi  = $serviceLocator->get('Vivo\CMS\Api\Content\File');
        $adapter  = new DefaultAdapter($fileApi);

        return $adapter;
    }

}
