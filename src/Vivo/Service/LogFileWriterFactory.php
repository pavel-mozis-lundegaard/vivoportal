<?php
namespace Vivo\Service;

use Zend\Log\Writer\Stream;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsFactory
 */
class LogFileWriterFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $date = date('Y-m-j');
        $writer = new Stream(__DIR__."/../../../../../data/logs/vivo_{$date}.log");
        return $writer;
    }
}
