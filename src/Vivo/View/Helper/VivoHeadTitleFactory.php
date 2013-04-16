<?php
namespace Vivo\View\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class HeadTitleFactory
 * @package Vivo\View\Helper
 */
class VivoHeadTitleFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm             = $serviceLocator->getServiceLocator();
        /** @var $cmsEvent \Vivo\CMS\Event\CMSEvent */
        $cmsEvent       = $sm->get('cms_event');
        $site           = $cmsEvent->getSite();
        if (is_null($site)) {
            throw new Exception\RuntimeException(sprintf("%s: Site object not available", __METHOD__));
        }
        $documentApi    = $sm->get('Vivo\CMS\Api\Document');
        $cmsApi         = $sm->get('Vivo\CMS\Api\CMS');
        $helper         = new VivoHeadTitle($cmsApi, $documentApi, $site);
        return $helper;
    }
}
