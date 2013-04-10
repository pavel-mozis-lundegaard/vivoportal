<?php
namespace Vivo\InputFilter;

use Vivo\InputFilter\Condition\ConditionInterface;
use Vivo\InputFilter\Condition\ConditionPluginManager;

use Zend\InputFilter\Factory as ZfInputFilterFactory;
use Zend\InputFilter\InputInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stdlib\ArrayUtils;

use Traversable;

/**
 * Class Factory
 * Creates VivoInputFilter input filters by default
 */
class Factory extends ZfInputFilterFactory
{
    /**
     * Key in the input filter specification containing the conditions definition
     * @var string
     */
    protected $conditionsKey    = '__conditions';

    /**
     * Condition plugin manager
     * @var ConditionPluginManager
     */
    protected $conditionPluginManager;

    /**
     * Constructor
     * @param ConditionPluginManager $conditionPluginManager
     */
    public function __construct(ConditionPluginManager $conditionPluginManager)
    {
        $this->conditionPluginManager   = $conditionPluginManager;
    }

    /**
     * Factory for input filters
     * @param  array|Traversable $inputFilterSpecification
     * @throws Exception\ConfigException
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * @return InputFilterInterface
     */
    public function createInputFilter($inputFilterSpecification)
    {
        if (!is_array($inputFilterSpecification) && !$inputFilterSpecification instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($inputFilterSpecification) ? get_class($inputFilterSpecification) : gettype($inputFilterSpecification))
            ));
        }
        if ($inputFilterSpecification instanceof Traversable) {
            $inputFilterSpecification = ArrayUtils::iteratorToArray($inputFilterSpecification);
        }

        //Use VivoInputFilter by default
        $class = 'Vivo\InputFilter\VivoInputFilter';
        if (isset($inputFilterSpecification['type']) && is_string($inputFilterSpecification['type'])) {
            $class = $inputFilterSpecification['type'];
            if (!class_exists($class)) {
                throw new Exception\RuntimeException(sprintf(
                    'Input factory expects the "type" to be a valid class; received "%s"',
                    $class
                ));
            }
            unset($inputFilterSpecification['type']);
        }
        $inputFilter = new $class();

        $this->addConditionsFromSpecification($inputFilter, $inputFilterSpecification);

        if (!$inputFilter instanceof InputFilterInterface) {
            throw new Exception\RuntimeException(sprintf(
                'InputFilter factory expects the "type" to be a class implementing %s; received "%s"',
                'Zend\InputFilter\InputFilterInterface', $class));
        }

        foreach ($inputFilterSpecification as $key => $value) {

            if (($value instanceof InputInterface)
                || ($value instanceof InputFilterInterface)
            ) {
                $input = $value;
            } else {
                $input = $this->createInput($value);
            }

            $inputFilter->add($input, $key);
        }

        return $inputFilter;
    }

    /**
     * Adds conditions to the input filter and removes the conditions definition from the input filter specification
     * @param InputFilterInterface $inputFilter
     * @param array $inputFilterSpecification
     * @return VivoInputFilter|InputFilterInterface
     * @throws Exception\ConfigException
     */
    public function addConditionsFromSpecification(InputFilterInterface $inputFilter, array &$inputFilterSpecification)
    {
        if (isset($inputFilterSpecification[$this->conditionsKey])) {
            if ($inputFilter instanceof VivoInputFilter) {
                foreach ($inputFilterSpecification[$this->conditionsKey] as $condition) {
                    if (is_array($condition)) {
                        $name   = $condition['name'];
                        if (isset($condition['options'])) {
                            $options    = $condition['options'];
                        } else {
                            $options    = null;
                        }
                        /** @var $newCondition ConditionInterface */
                        $condition  = $this->conditionPluginManager->get($name, $options);
                    }
                    if (!$condition instanceof ConditionInterface) {
                        throw new Exception\ConfigException(
                            sprintf("%s: Condition must be either a ConditionInterface object or an array", __METHOD__));
                    }
                    $inputFilter->addCondition($condition);
                }
            }
            unset($inputFilterSpecification[$this->conditionsKey]);
        }
        return $inputFilter;
    }
}
