<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DbServiceManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbSm   = new DbServiceManager();
        $pdoAf  = $serviceLocator->get('pdo_abstract_factory');
        $dbSm->addAbstractFactory($pdoAf);
        return $dbSm;
    }
}