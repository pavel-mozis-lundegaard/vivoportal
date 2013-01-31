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
        $cf = new ComponentFactory($serviceLocator,
                $serviceLocator->get('di_proxy'), $serviceLocator->get('cms'),
                $serviceLocator->get('site_event')->getSite());

        $resolver = new ComponentResolver($serviceLocator->get('cms_config'));
        $cf->setResolver($resolver);
        return $cf;
    }
}
