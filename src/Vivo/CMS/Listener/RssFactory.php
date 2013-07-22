<?php
namespace Vivo\CMS\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RssFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @return Vivo\CMS\Listener\Rss
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Rss($serviceLocator->get('request'));
    }

}
