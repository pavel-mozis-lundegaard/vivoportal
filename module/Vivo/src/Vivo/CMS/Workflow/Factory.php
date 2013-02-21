<?php
namespace Vivo\CMS\Workflow;

use Vivo\CMS;

use Zend\ServiceManager\ServiceManager;

/**
 * Factory
 * Workflow factory
 */
class Factory
{
    /**
     * Service Manager
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm       = $sm;
    }

    /**
     * Creates and returns a workflow instance
     * @param string $name Workflow name
     * @return WorkflowInterface
     * @throws Exception\InvalidArgumentException
     */
    public function get($name)
    {
        $workflow   = $this->sm->get($name);
        return $workflow;
    }
}
