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
        $diProxy        = $serviceLocator->get('di_proxy');
        $cms            = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $documentApi    = $serviceLocator->get('Vivo\CMS\Api\Document');
        $site           = $serviceLocator->get('site_event')->getSite();
        $cf             = new ComponentFactory($serviceLocator, $diProxy, $cms, $documentApi, $site);
        $resolver       = new ComponentResolver($serviceLocator->get('cms_config'));
        $cf->setResolver($resolver);
        return $cf;
    }
}
