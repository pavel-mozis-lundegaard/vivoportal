<?php
namespace Vivo\CMS\UI\Manager\Explorer;

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
        $sm = $serviceLocator->get('service_manager');
        $editor = new Editor($sm->get('Vivo\CMS\Api\CMS'), $sm->get('metadata_manager'));
        $editor->setTabContainer($sm->create('Vivo\UI\TabContainer'), 'contentTab');
        return $editor;
    }
}
