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
        $cmsApi             = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $documentApi        = $serviceLocator->get('Vivo\CMS\Api\Document');
        $pathBuilder        = $serviceLocator->get('path_builder');
        $alert              = $serviceLocator->get('Vivo\UI\Alert');
        $urlHelper          = $serviceLocator->get('Vivo\Util\UrlHelper');
        $copy               = new Copy($cmsApi, $documentApi, $pathBuilder, $alert, $urlHelper);
        return $copy;
    }
}
