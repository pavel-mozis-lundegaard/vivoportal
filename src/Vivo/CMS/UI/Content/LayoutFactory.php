<?php
namespace Vivo\CMS\UI\Content;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LayoutFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Layout
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $layout = new Layout($serviceLocator->get('Vivo\CMS\Api\CMS'));
        return $layout;
    }
}
