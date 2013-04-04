<?php
namespace Vivo\Validator;

use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

use Traversable;

/**
 * Class Conditional
 * Validates conditionally
 * @package Vivo\validator
 */
class Conditional extends AbstractValidator
{
    /**
     * Input filter factory
     * @var InputFilterFactory
     */
    protected $inputFilterFactory;

    /**
     * Name of the field on which the condition is based
     * Ex. array('firstName'): The condition is based on the 'firstName' field
     * Ex. array('address', 'street'): The condition is based on the 'street' field which is
     * present in the 'address' fieldset (multiple nested fieldset may be specified in this manner)
     * @var array
     */
    protected $condField;

    /**
     * Input configuration used for the cond field
     * Input applied to the cond field
     * The actual validation will either be or not be performed based on the validation result of this input
     * @var array
     */
    protected $condInputConfig      = array();

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
        if (!is_array($context)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Context must be an array", __METHOD__));
        }
        $data       = $context;
        $condField  = $this->getCondField();
        while ($name = array_shift($condField)) {
            if (!array_key_exists($name, $data)) {
                throw new Exception\RuntimeException(
                    sprintf("%s: The cond field part '%s' is missing in context", __METHOD__, $name));
            }
            $data   = $data[$name];
        }
        $condFieldInput = $this->inputFilterFactory->createInput($this->getCondInputConfig());
        $condFieldInput->setValue($data);
        if ($condFieldInput->isValid($context) === $this->getValidateWhenCondIs()) {
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
     * Sets conditional field
     * @param string|array $condField
     * @throws Exception\ConfigException
     */
    public function setCondField($condField)
    {
        if (is_string($condField)) {
            $condField  = array($condField);
        }
        if (!is_array($condField)) {
            //Unsupported cond field format
            throw new Exception\ConfigException(
                sprintf("%s: Cond field must be either a string or an array", __METHOD__));
        }
        $this->condField    = $condField;
    }

    /**
     * Returns the conditional field
     * @return array
     */
    public function getCondField()
    {
        return $this->condField;
    }

    /**
     * Sets cond field input configuration
     * @param array|Traversable $condInputConfig
     * @throws Exception\InvalidArgumentException
     */
    public function setCondInputConfig($condInputConfig)
    {
        if ($condInputConfig instanceof Traversable) {
            $condInputConfig = ArrayUtils::iteratorToArray($condInputConfig);
        }
        if (!is_array($condInputConfig)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Cond input config must be either an array or Traversable", __METHOD__));
        }
        $this->condInputConfig = $condInputConfig;
    }

    /**
     * Returns cond field input configuration
     * @return array
     */
    public function getCondInputConfig()
    {
        return $this->condInputConfig;
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
}
