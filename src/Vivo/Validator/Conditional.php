<?php
namespace Vivo\Validator;

use Vivo\InputFilter\Condition\ConditionAwareInterface;
use Vivo\Service\Initializer\InputFilterFactoryAwareInterface;

use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

use Traversable;

/**
 * Class Conditional
 * Validates conditionally
 * @package Vivo\validator
 */
class Conditional extends AbstractValidator implements ConditionAwareInterface, InputFilterFactoryAwareInterface
{
    /**
     * Default value of the condition - used when the condition is not set
     * @var boolean
     */
    protected $defaultConditionValue    = true;

    /**
     * Value of the condition
     * @var boolean
     */
    private $conditionValue;

    /**
     * Input filter factory
     * @var InputFilterFactory
     */
    protected $inputFilterFactory;

    /**
     * Perform validation when the validation of the cond field input returns this value
     * When set to true, the actual validation will be performed only when the cond field validation returns true
     * When set to false, the actual validation will be performed only when the cond field validation returns false
     * (When the actual validation is not performed, this validator returns true)
     * @var bool
     */
    protected $validateWhenCondIs   = true;

    /**
     * Input configuration used for the tested value
     * This is the actual input configuration used for validation when the cond input validation returns the value
     * prescribed in $this->validateWhenCondIs
     * @var array
     */
    protected $inputConfig          = array();

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param $context
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * @throws Exception\ConfigException
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        if ($this->getConditionValue() === $this->getValidateWhenCondIs()) {
            //Perform validation
            $input  = $this->inputFilterFactory->createInput($this->getInputConfig());
            $input->setValue($value);
            $valid  = $input->isValid($context);
            if (!$valid) {
                $messages   = $input->getMessages();
                $this->abstractOptions['messages']  = $messages;
            }
        } else {
            //Do not perform validation - the input is considered valid
            $valid  = true;
        }
        return $valid;
    }

    /**
     * Sets the input filter factory
     * @param InputFilterFactory $inputFilterFactory
     */
    public function setInputFilterFactory(InputFilterFactory $inputFilterFactory)
    {
        $this->inputFilterFactory   = $inputFilterFactory;
    }

    /**
     * Sets on which cond field validation result the actual validation should be performed
     * @param boolean $validateWhenCondIs
     */
    public function setValidateWhenCondIs($validateWhenCondIs)
    {
        $this->validateWhenCondIs = (bool) $validateWhenCondIs;
    }

    /**
     * Returns on which cond field validation result the actual validation should be performed
     * @return boolean
     */
    public function getValidateWhenCondIs()
    {
        return $this->validateWhenCondIs;
    }

    /**
     * Sets the actual input configuration which should be performed based on the cond validation
     * @param array|Traversable $inputConfig
     * @throws Exception\InvalidArgumentException
     */
    public function setInputConfig($inputConfig)
    {
        if ($inputConfig instanceof Traversable) {
            $inputConfig = ArrayUtils::iteratorToArray($inputConfig);
        }
        if (!is_array($inputConfig)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Input config must be either an array or a Traversable", __METHOD__));
        }
        $this->inputConfig = $inputConfig;
    }

    /**
     * @return array
     */
    public function getInputConfig()
    {
        return $this->inputConfig;
    }

    /**
     * Returns the current condition value
     * @return bool
     */
    public function getConditionValue()
    {
        if (is_null($this->conditionValue)) {
            $this->conditionValue   = $this->defaultConditionValue;
        }
        return $this->conditionValue;
    }

    /**
     * Sets the current value of the condition
     * @param bool $condition
     * @return void
     */
    public function setConditionValue($condition)
    {
        $this->conditionValue   = (bool) $condition;
    }

    /**
     * Sets the default condition value
     * @param boolean $defaultConditionValue
     */
    public function setDefaultConditionValue($defaultConditionValue)
    {
        $this->defaultConditionValue = (bool) $defaultConditionValue;
    }

    /**
     * Returns the default condition value
     * @return boolean
     */
    public function getDefaultConditionValue()
    {
        return $this->defaultConditionValue;
    }
}
