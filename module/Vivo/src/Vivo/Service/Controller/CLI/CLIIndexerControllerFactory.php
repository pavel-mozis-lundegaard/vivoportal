<?php
namespace Vivo\Service\Controller\CLI;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for CLI\Repository controller.
 */
class CLIIndexerControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm             = $serviceLocator->getServiceLocator();
        $indexer        = $sm->get('indexer');
        $controller     = new \Vivo\Controller\CLI\IndexerController($indexer);
        return $controller;
    }
}
