<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentFactory implements FactoryInterface
{
    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface
     * @return \Vivo\Backend\UI\Explorer\Editor\Content
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->get('service_manager');
        $documentApi = $sm->get('Vivo\CMS\Api\Document');
        $metadataManager = $sm->get('metadata_manager');
        $lookupDataManager = $sm->get('lookup_data_manager');

        return new Content($sm, $documentApi, $metadataManager, $lookupDataManager);
    }

}
