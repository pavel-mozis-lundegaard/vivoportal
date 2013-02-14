<?php
namespace Vivo\Backend;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for CMSFrontController
 */
class BackendControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $bc = new BackendController();


        $sm = $serviceLocator->getServiceLocator();
        $siteEvent = $sm->get('site_event');

        //$ctc = new \Vivo\UI\ComponentTreeController($sm->get('session_manager'), $sm->get('request'));
        $bc->setComponentTreeController($sm->get('Vivo\UI\ComponentTreeController'));
        $bc->setSiteEvent($siteEvent);
        $bc->setRedirector($sm->get('redirector'));
        $bc->setSM($sm);

        return $bc;
    }
}
