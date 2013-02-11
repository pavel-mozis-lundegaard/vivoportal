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
        $fc = new BackendController();
        $sm = $serviceLocator->getServiceLocator();
        $siteEvent = $sm->get('site_event');
//        $fc->setTreeUtil($sm->get('di')->get('Vivo\UI\ComponentTreeController'));

        $ctc = new \Vivo\UI\ComponentTreeController($sm->get('session_manager'), $sm->get('request'));
        $fc->setComponentTreeController($ctc);
        $fc->setCMS($sm->get('cms'));
        $fc->setSiteEvent($siteEvent);
        return $fc;
    }
}
