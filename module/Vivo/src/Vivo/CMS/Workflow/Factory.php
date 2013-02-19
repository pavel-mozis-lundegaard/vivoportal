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
//    /**
//     * Factory options
//     * @var array
//     */
//    protected $options  = array(
//        //Workflow names mapped to workflow classes
//        'classMap'  => array(
//            'workflow_basic'    => 'Vivo\CMS\Workflow\Basic',
//        ),
//    );

    /**
     * Service Manager
     * @var ServiceManager
     */
    protected $sm;

//    /**
//     * Workflows instantiated so far
//     * @var WorkflowInterface[]
//     */
//    protected $workflows    = array();

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
//        $name   = $this->getWorkflowServiceName($name);
        $workflow   = $this->sm->get($name);
        return $workflow;
//        if (!array_key_exists($name, $this->workflows)) {
//            if (!isset($this->options['classMap'][$name])) {
//                throw new Exception\InvalidArgumentException(
//                    sprintf("%s: Unknown workflow name '%s'", __METHOD__, $name));
//            }
//            $class                  = $this->options['classMap'][$name];
//            $workflow               = new $class();
//            $this->workflows[$name] = $workflow;
//        }
//        return $this->workflows[$name];
    }

//    /**
//     * Returns normalized workflow name (converts workflow class to name)
//     * @param string $name
//     * @return string
//     */
//    protected function getWorkflowServiceName($name)
//    {
//        if (array_key_exists($name, $this->options['classMap'])) {
//            return $name;
//        }
//        if (in_array($name, $this->options['classMap'])) {
//            return array_search($name, $this->options['classMap']);
//        }
//        return $name;
//    }
}
