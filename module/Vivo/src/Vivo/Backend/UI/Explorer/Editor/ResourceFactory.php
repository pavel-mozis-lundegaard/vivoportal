<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResourceFactory implements FactoryInterface
{
    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface
     * @return \Vivo\Backend\UI\Explorer\Editor\Resource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cmsApi = $serviceLocator->get('Vivo\CMS\Api\CMS');

        $component = new Resource($cmsApi);

        return $component;
    }

}
