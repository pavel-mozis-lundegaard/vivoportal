<?php
namespace Vivo\CMS\Service\UI\Content;

use Vivo\CMS\UI\Content\Layout;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LayoutFactory implements FactoryInterface
{
    /**
     * Create UI Page object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $layout = new Layout($serviceLocator->get('Vivo\CMS\Api\CMS'));
        return $layout;
    }
}
