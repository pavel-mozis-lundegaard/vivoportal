<?php
namespace Vivo\Service;

use Vivo\CMS\ComponentFactory;
use Vivo\CMS\ComponentResolver;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ComponentFactoryFactory
 */
class ComponentFactoryFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di = $serviceLocator->get('vivo_service_manager')->get('di_proxy');
        $cf = new ComponentFactory($di, $serviceLocator->get('cms'), $serviceLocator->get('site_event')->getSite());
        $resolver = new ComponentResolver($serviceLocator->get('config'));
        $cf->setResolver($resolver);
        return $cf;
    }
}
