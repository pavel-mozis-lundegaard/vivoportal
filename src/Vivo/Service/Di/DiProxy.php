<?php
namespace Vivo\Service\Di;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Di\Di;

use Zend\ServiceManager\Di\DiServiceFactory;

class DiProxy extends DiServiceFactory
{
    public function __construct(Di $di, ServiceLocatorInterface $serviceLocator, array $parameters = array(), $useServiceLocator = self::USE_SL_BEFORE_DI) {
        parent::__construct($di, null, $parameters, $useServiceLocator);
        $this->serviceLocator = $serviceLocator;
    }
}