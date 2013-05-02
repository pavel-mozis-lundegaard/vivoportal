<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cmsApi             = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $adapter            = new DefaultAdapter($cmsApi);

        return $adapter;
    }

}
