<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Exception;

use Zend\InputFilter\Input;
use Zend\Stdlib\ArrayUtils;

use Traversable;

/**
 * AbstractCondition
 */
abstract class AbstractCondition implements ConditionInterface
{
    /**
     * Array of conditional validators
     * array(
     *      'input'             => array(),
     *      'validator_class'   => string|null,
     * )
     * @var array
     */
    protected $conditionalValidators    = array();

    /**
     * Constructor
     * @param null $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (is_array($options)) {
            if (array_key_exists('conditionalValidators', $options)) {
                $this->setConditionalValidators($options['conditionalValidators']);
            }
        }
    }

    /**
     * Sets conditional validators
     * @param array $conditionalValidators
     * @throws \Vivo\InputFilter\Exception\ConfigException
     */
    public function setConditionalValidators(array $conditionalValidators)
    {
        foreach ($conditionalValidators as $conditionalValidator) {
            if (!array_key_exists('input', $conditionalValidator)) {
                throw new Exception\ConfigException(
                    sprintf("%s: 'input' key missing in conditional validator configuration",
                        __METHOD__));
            }
            if (is_string($conditionalValidator['input'])) {
                $conditionalValidator['input']   = array($conditionalValidator['input']);
            }
            if (!is_array($conditionalValidator['input'])) {
                throw new Exception\ConfigException(
                    sprintf("%s: Input part of a conditional validator must be defined as either " .
                        "a string or an array", __METHOD__));
            }
            if (!isset($conditionalValidator['validatorClass'])) {
                $conditionalValidator['validatorClass']    = null;
            }
            $this->conditionalValidators[]  = $conditionalValidator;
        }
    }

    /**
     * Returns an array of validators which will be injected with the result
     * of the getConditionValue() method
     * Returns:
     * array(
     *      array(
     *          'input'             => array(),
     *          'validator_class'   => string|null,
     *      ), ...
     * )
     * @return array
     */
    public function getConditionalValidators()
    {
        return $this->conditionalValidators;
    }
}
