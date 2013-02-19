<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * MoveFactory
 */
class MoveFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $documentApi        = $serviceLocator->get('Vivo\CMS\Api\Document');
        $pathBuilder        = $serviceLocator->get('path_builder');
        $move               = new Move($documentApi, $pathBuilder);
        return $move;
    }
}
