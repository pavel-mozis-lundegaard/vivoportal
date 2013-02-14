<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Editor factory.
 */
class EditorFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm                 = $serviceLocator->get('service_manager');
        $metadataManager    = $sm->get('metadata_manager');
        $documentApi        = $sm->get('Vivo\CMS\Api\Document');
        $editor             = new Editor($sm, $metadataManager, $documentApi);
        $editor->setTabContainer($sm->create('Vivo\UI\TabContainer'), 'contentTab');
        return $editor;
    }
}
