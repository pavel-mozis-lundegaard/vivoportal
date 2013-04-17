<?php
namespace Vivo\UI;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaginatorFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    	$request            = $serviceLocator->get('request');

        return new Paginator($request);
    }
}
