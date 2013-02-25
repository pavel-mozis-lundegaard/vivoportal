<?php
namespace Vivo\CMS\Workflow;

use Vivo\CMS\Workflow\WorkflowInterface;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * BasicFactory
 */
class BasicFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $parameters = array(
            WorkflowInterface::STATE_ARCHIVED   => array(),
            WorkflowInterface::STATE_NEW        => array(),
            WorkflowInterface::STATE_PUBLISHED  => array(),
        );
        $service    = new Basic($parameters);
        return $service;
    }
}
