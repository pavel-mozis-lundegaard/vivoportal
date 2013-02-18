<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Wiever factory.
 */
class ViewerFactory implements FactoryInterface
{
    /**
     * Create Viewer
     * @param ServiceLocatorInterface $serviceLocator
     * @return Viewer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $viewer = new Viewer($serviceLocator->get('Vivo\CMS\Api\CMS'));
        return $viewer;
    }
}
