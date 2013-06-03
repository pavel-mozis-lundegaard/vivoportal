<?php
namespace Vivo\CMS\UI\Content;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FileboardFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return File
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fileboardApi = $serviceLocator->get('Vivo\CMS\Api\Content\Fileboard');

        return new Fileboard($fileboardApi);
    }
}
