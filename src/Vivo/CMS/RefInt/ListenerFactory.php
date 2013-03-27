<?php
namespace Vivo\CMS\RefInt;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ListenerFactory
 */
class ListenerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');
        $listener           = new Listener($symRefConvertor);
        return $listener;
    }
}
