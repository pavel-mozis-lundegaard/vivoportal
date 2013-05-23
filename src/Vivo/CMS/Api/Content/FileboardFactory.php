<?php
namespace Vivo\CMS\Api\Content;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FileboardFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Vivo\CMS\Api\Content\Fileboard
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cms                    = $serviceLocator->get('Vivo\CMS\Api\CMS');
//         $repository             = $serviceLocator->get('repository');
//         $pathBuilder            = $serviceLocator->get('path_builder');
//         $uuidGenerator          = $serviceLocator->get('uuid_generator');
//         $translitDocTitleToPath = $serviceLocator->get('Vivo\Transliterator\DocTitleToPath');
//         $config                 = $serviceLocator->get('config');
        $api                    = new Fileboard($cms);
        return $api;
    }
}
