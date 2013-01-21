<?php
namespace Vivo\CMS\Service\UI\Content;

use Vivo\CMS\UI\Content\File;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FileFactory implements FactoryInterface
{
    /**
     * Create UI Page object.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new File($serviceLocator->get('Vivo\CMS\Api\CMS'));
    }
}
