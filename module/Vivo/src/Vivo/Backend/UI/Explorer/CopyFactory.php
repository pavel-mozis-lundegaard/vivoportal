<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CopyFactory
 */
class CopyFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $documentApi        = $serviceLocator->get('Vivo\CMS\Api\Document');
        $copy               = new Copy($documentApi);
        return $copy;
    }
}
