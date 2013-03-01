<?php
namespace Vivo\Util;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * UrlHelper factory
 */
class UrlHelperFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return UrlHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $urlHelper = new UrlHelper($serviceLocator->get('router'),
                $serviceLocator->get('application')->getMvcEvent()->getRouteMatch()
                );
        return $urlHelper;
    }
}
