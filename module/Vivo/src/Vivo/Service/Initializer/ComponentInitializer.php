<?php
namespace Vivo\Service\Initializer;

use Vivo\View\Model\UIViewModel;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ComponentInitializer implements InitializerInterface
{
    public function initialize($instance,
            ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof \Vivo\UI\Component) {
            $instance->setView($serviceLocator->get('view_model'));
        }
    }
}
