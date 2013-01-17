<?php
namespace Vivo\Service\UI;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class PageFactory implements FactoryInterface
{
    /**
     * Create UI Page object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $page = new \Vivo\UI\Page($serviceLocator->get('response'),
                $config['vivo']['ui']['Vivo\UI\Page']);
        $page->setView($serviceLocator->get('view_model'));
        return $page;
    }
}
