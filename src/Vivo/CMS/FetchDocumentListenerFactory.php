<?php
namespace Vivo\CMS;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FetchDocumentListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Vivo\CMS\FetchErrorDocumentListener
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $cms = $sm->get('Vivo\CMS\Api\CMS');

        $listener = new FetchDocumentListener($cms);

        return $listener;
    }
}
