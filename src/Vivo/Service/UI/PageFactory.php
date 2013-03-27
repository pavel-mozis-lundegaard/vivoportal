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
        $config = $serviceLocator->get('cms_config');
        $page = new \Vivo\UI\Page($serviceLocator->get('response'),
                $config['ui']['Vivo\UI\Page']);
        return $page;
    }
}
