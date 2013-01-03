<?php
namespace Vivo\Service;

use Zend\Di;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiFactory implements FactoryInterface
{
    /**
     * Creates and return Di instance.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di = new Di\Di();
        $config = $serviceLocator->get('Configuration');
        if (isset($config['di'])) {
            $di->configure(new Di\Config($config['di']));
        }
        return $di;
    }
}
