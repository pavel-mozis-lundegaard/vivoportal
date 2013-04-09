<?php
namespace Vivo\Validator;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Initializer
 * Validator initializer
 */
class Initializer implements InitializerInterface
{
    /**
     * Initialize
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof \Vivo\Service\Initializer\InputFilterFactoryAwareInterface) {
            $sm                 = $serviceLocator->getServiceLocator();
            $inputFilterFactory = $sm->get('input_filter_factory');
            $instance->setInputFilterFactory($inputFilterFactory);
        }
    }
}
