<?php
namespace Vivo\CMS\Workflow;

use Vivo\CMS;

/**
 * Factory
 * Workflow factory
 */
class Factory
{
    /**
     * Factory options
     * @var array
     */
    protected $options  = array(
        //Workflow names mapped to workflow classes
        'classMap'  => array(
            'basic'     => 'Vivo\CMS\Workflow\Basic',
        ),
    );

    /**
     * Workflows instantiated so far
     * @var WorkflowInterface[]
     */
    protected $workflows;

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->classMap = array_merge($this->options, $options);
    }

    /**
     * Creates and returns a workflow instance
     * @param string $name Workflow name
     * @return WorkflowInterface
     * @throws Exception\InvalidArgumentException
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->workflows)) {
            if (!isset($this->options['classMap'][$name])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Unknown workflow name '%s'", __METHOD__, $name));
            }
            $class                  = $this->options['classMap'][$name];
            $workflow               = new $class();
            $this->workflows[$name] = $workflow;
        }
        return $this->workflows[$name];
    }
}
