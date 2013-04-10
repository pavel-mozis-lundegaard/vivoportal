<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Factory as VivoInputFilterFactory;
use Vivo\InputFilter\Exception;
use Vivo\Service\Initializer\InputFilterFactoryAwareInterface;

use Zend\Stdlib\ArrayUtils;
use Zend\InputFilter\Factory as ZfInputFilterFactory;

use Traversable;

/**
 * Input
 * Condition expressed as ZF2 Input
 */
class Input extends AbstractCondition implements InputFilterFactoryAwareInterface
{
    /**
     * Input filter factory
     * @var VivoInputFilterFactory
     */
    protected $inputFilterFactory;

    /**
     * Name of the field on which the condition is based
     * Ex. array('firstName'): The condition is based on the 'firstName' field
     * Ex. array('address', 'street'): The condition is based on the 'street' field which is
     * present in the 'address' fieldset (multiple nested fieldsets may be specified in this manner)
     * @var array
     */
    protected $field;

    /**
     * Input configuration used for the cond field
     * Input applied to the cond field
     * The actual validation will either be or not be performed based on the validation result of this input
     * @var array
     */
    protected $inputConfig      = array();

    /**
     * Constructor
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (is_array($options)) {
            if (array_key_exists('field', $options)) {
                $this->setField($options['field']);
            }
            if (array_key_exists('inputConfig', $options)) {
                $this->setInputConfig($options['inputConfig']);
            }
        }
        parent::__construct($options);
    }

    /**
     * Returns condition value
     * @param array $data
     * @throws \Vivo\InputFilter\Exception\ConfigException
     * @throws \Vivo\InputFilter\Exception\RuntimeException
     * @return boolean
     */
    public function getConditionValue(array $data)
    {
        $field      = $this->getField();
        if (!is_array($field)) {
            throw new Exception\ConfigException(sprintf("%s: Field not set", __METHOD__));
        }
        $inputValue = $data;
        while ($name = array_shift($field)) {
            if (!array_key_exists($name, $inputValue)) {
                $inputValue = null;
                break;
            }
            $inputValue = $inputValue[$name];
        }
        $input  = $this->inputFilterFactory->createInput($this->getInputConfig());
        $input->setValue($inputValue);
        $conditionValue = $input->isValid($data);
        return $conditionValue;
    }

    /**
     * Sets the input filter factory
     * @param ZfInputFilterFactory $inputFilterFactory
     */
    public function setInputFilterFactory(ZfInputFilterFactory $inputFilterFactory)
    {
        $this->inputFilterFactory   = $inputFilterFactory;
    }

    /**
     * Sets conditional field
     * @param string|array $condField
     * @throws Exception\ConfigException
     */
    public function setField($condField)
    {
        if (is_string($condField)) {
            $condField  = array($condField);
        }
        if (!is_array($condField)) {
            //Unsupported cond field format
            throw new Exception\ConfigException(
                sprintf("%s: Cond field must be either a string or an array", __METHOD__));
        }
        $this->field    = $condField;
    }

    /**
     * Returns the conditional field
     * @return array
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Sets cond field input configuration
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
                sprintf("%s: Cond input config must be either an array or Traversable", __METHOD__));
        }
        $this->inputConfig = $inputConfig;
    }

    /**
     * Returns cond field input configuration
     * @return array
     */
    public function getInputConfig()
    {
        return $this->inputConfig;
    }
}
