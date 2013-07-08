<?php
namespace Vivo\UI;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ListenerProviderInterface
 * UI components implementing this interface provide listeners to be attached
 */
interface ListenerProviderInterface
{
    /**
     * Attaches listeners
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function attachListeners(ServiceLocatorInterface $serviceLocator);
}