<?php
namespace Vivo\Service;

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
        $di = new Di();
        $config = $serviceLocator->get('Configuration');
        if (isset($config['di'])) {
            $di->configure(new DiConfiguration($config['di']));
        }
        return $di;
    }
}
