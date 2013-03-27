<?php
namespace Vivo\CMS\UI\Content;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class HyperlinkFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Hyperlink
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Hyperlink($serviceLocator->get('redirector'));
    }
}
