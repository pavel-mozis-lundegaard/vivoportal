<?php
namespace Vivo\Util;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * MIME factory
 */
class MIMEFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Vivo\Util\MIME
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $types  = $config['mime']['types'];

        return new MIME($types);
    }
}
