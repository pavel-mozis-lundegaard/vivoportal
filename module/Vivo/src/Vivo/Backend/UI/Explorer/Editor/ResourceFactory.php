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
        $sm = $serviceLocator->get('service_manager');
        $cmsApi = $sm->get('Vivo\CMS\Api\CMS');
        $alert = $sm->get('Vivo\UI\Alert');

        $component = new Resource($cmsApi);
        $component->setAlert($alert);

        return $component;
    }

}
