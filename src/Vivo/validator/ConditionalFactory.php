<?php
namespace Vivo\Validator;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class ConditionalFactory
 * @package Vivo\Validator
 */
class ConditionalFactory implements FactoryInterface
{
    /**
     * Creation options
     * @var array
     */
    protected $options;

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $this->options  = $options;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //Get the main service manager
        $sm                 = $serviceLocator->getServiceLocator();
        $inputFilterFactory = $sm->get('input_filter_factory');
        $validator          = new Conditional($this->options);
        $validator->setInputFilterFactory($inputFilterFactory);
        return $validator;
    }
}
