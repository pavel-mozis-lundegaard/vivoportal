<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentTabFactory implements FactoryInterface
{
    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface
     * @return \Vivo\Backend\UI\Explorer\Editor\ContentTab
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->get('service_manager');
        $documentApi = $sm->get('Vivo\CMS\Api\Document');

        $component = new ContentTab($sm, $documentApi);

        return $component;
    }

}
