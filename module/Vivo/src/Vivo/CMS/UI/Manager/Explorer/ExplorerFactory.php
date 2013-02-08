<?php
namespace Vivo\CMS\UI\Manager\Explorer;

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
        $sm = $serviceLocator->get('service_manager');
        $siteSelector = $sm->get('Vivo\CMS\UI\Manager\SiteSelector');

        $explorer = new \Vivo\CMS\UI\Manager\Explorer\Explorer($sm->get('Vivo\CMS\Api\CMS'),
                $siteSelector);

        $explorer->setEventManager($sm->get('event_manager'));

        $explorer->addComponent($sm->create('Vivo\CMS\UI\Manager\Explorer\Ribbon'), 'ribbon');

        //add components
        $explorer->addComponent($sm->create('Vivo\CMS\UI\Manager\Explorer\Browser'), 'browser');
        $explorer->addComponent($sm->create('Vivo\CMS\UI\Manager\Explorer\Viewer'), 'viewer');
        $explorer->addComponent($sm->create('Vivo\CMS\UI\Manager\Explorer\Editor'), 'editor');

        $tree = new \Vivo\CMS\UI\Manager\Explorer\Tree();
        $tree->setView($sm->get('view_model'));
        $tree->setEntityManager($explorer);
        $explorer->addComponent($tree, 'tree');

        $finder = new \Vivo\CMS\UI\Manager\Explorer\Finder();
        $finder->setEntityManager($explorer);
        $finder->setView($sm->get('view_model'));
        $explorer->addComponent($finder, 'finder');

        return $explorer;
    }
}
