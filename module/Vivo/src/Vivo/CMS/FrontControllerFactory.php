<?php
namespace Vivo\CMS;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for FrontController
 */
class FrontControllerFactory implements FactoryInterface
{
    /**
     * Creates CMS front controller.
     * @param ServiceLocatorInterface $serviceLocator
     * @return FrontController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fc = new FrontController();
        $sm = $serviceLocator->getServiceLocator();
        $siteEvent = $sm->get('site_event');
        if ($siteEvent->getSite()) {
            $fc->setComponentFactory($sm->get('component_factory'));
        }
        $fc->setComponentTreeController($sm->get('Vivo\UI\ComponentTreeController'));
        $fc->setCMS($sm->get('Vivo\CMS\Api\CMS'));
        $fc->setSiteEvent($siteEvent);
        $fc->setRedirector($sm->get('redirector'));
        $fc->setUrlHelper($sm->get('Vivo\Util\UrlHelper'));
        return $fc;
    }
}
