<?php
namespace Vivo\CMS\Api\Manager;

use Vivo\CMS\Api\Manager\Manager;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cms        = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $repository = $serviceLocator->get('repository');
        $indexerApi = $serviceLocator->get('Vivo\CMS\Api\Indexer');
        return new Manager($cms, $repository, $indexerApi);
    }
}
