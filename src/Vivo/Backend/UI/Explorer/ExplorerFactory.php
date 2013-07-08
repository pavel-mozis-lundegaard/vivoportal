<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Explorer factory
 */
class ExplorerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm                         = $serviceLocator->get('service_manager');
        /** @var $componentCreator \Vivo\UI\ComponentCreator */
        $componentCreator           = $sm->get('Vivo\component_creator');
        $siteSelector               = $componentCreator->createComponent('Vivo\Backend\UI\SiteSelector');
        $ribbon                     = $componentCreator->createComponent('Vivo\Backend\UI\Explorer\Ribbon');
        $tree                       = $componentCreator->createComponent('Vivo\Backend\UI\Explorer\Tree');
        $finder                     = $componentCreator->createComponent('Vivo\Backend\UI\Explorer\Finder');
        $cmsApi                     = $sm->get('Vivo\CMS\Api\CMS');
        $componentTreeController    = $sm->get('component_tree_controller');
        $eventManager               = $sm->get('event_manager');
        $explorer = new Explorer($cmsApi, $siteSelector, $sm);
        $explorer->setComponentTreeController($componentTreeController);
        $explorer->setEventManager($eventManager);
        $explorer->addComponent($ribbon, 'ribbon');
        $tree->setExplorer($explorer);
        $explorer->addComponent($tree, 'tree');
        $finder->setExplorer($explorer);
        $explorer->addComponent($finder, 'finder');
        return $explorer;
    }
}
