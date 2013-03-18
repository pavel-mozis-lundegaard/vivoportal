<?php
namespace Vivo\Service\Initializer;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Initializer for UI components.
 */
class ComponentInitializer implements InitializerInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\InitializerInterface::initialize()
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof \Vivo\UI\Component) {
            //inject view model
            $instance->setView($serviceLocator->get('view_model'));
        }
    }
}
