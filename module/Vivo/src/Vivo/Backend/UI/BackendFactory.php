<?php
namespace Vivo\Backend\UI;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for CMSFrontController
 */
class BackendFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $backend = new Backend();
        $sm = $serviceLocator->get('service_manager');

        $headerBar = $sm->get('Vivo\Backend\UI\HeaderBar');

        $headerBar->addComponent($sm->get('Vivo\Backend\UI\SiteSelector'), 'siteSelector');
        $headerBar->addComponent($sm->get('Vivo\UI\Alert'), 'alert');




        $backend->addComponent($headerBar, 'headerBar');
        $backend->addComponent($sm->get('Vivo\Backend\UI\ModulesPanel'), 'modulesPanel');
        $backend->addComponent($sm->get('Vivo\Backend\UI\FooterBar'), 'footerBar');

        $backend->addComponent($sm->get('Vivo\Backend\UI\Explorer\Explorer'), 'module');

        return $backend;
    }
}
