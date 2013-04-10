<?php
namespace Vivo\InputFilter;

use Vivo\InputFilter\Condition\ConditionInterface;
use Vivo\InputFilter\Condition\ConditionAwareInterface;

use Zend\InputFilter\InputInterface;
use Zend\InputFilter\InputFilter as ZfInputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * VivoInputFilter
 * Adds parent context
 */
class VivoInputFilter extends ZfInputFilter
{
    /**
     * Form condtions
     * @var ConditionInterface[]
     */
    protected $conditions   = array();

    /**
     * Applies defined conditions
     * @throws Exception\RuntimeException
     */
    public function applyConditions()
    {
        //Apply all conditions defined in this input filter
        foreach ($this->conditions as $condition) {
            $condValue  = $condition->getConditionValue($this->data);
            foreach ($condition->getConditionalValidators() as $condValidatorSpec) {
                $input          = $this->getInputBySpec($condValidatorSpec['input']);
                if (isset($condValidatorSpec['validator_class'])) {
                    $validatorClass = $condValidatorSpec['validator_class'];
                } else {
                    $validatorClass = null;
                }
                $validators     = $input->getValidatorChain()->getValidators();
                foreach ($validators as $valSpec) {
                    /** @var $validator \Zend\Validator\ValidatorInterface */
                    $validator  = $valSpec['instance'];
                    if ($validator instanceof ConditionAwareInterface
                            && (is_null($validatorClass) || ($validator instanceof $validatorClass))) {
                        $validator->setConditionValue($condValue);
                    }
                }
            }
        }
    }

    /**
     * Finds and returns an Input object according to its specification as an array
     * @param array $inputNameSpec
     * @return InputInterface
     * @throws Exception\ConfigException
     * @throws Exception\RuntimeException
     */
    public function getInputBySpec(array $inputNameSpec)
    {
        $part   = array_shift($inputNameSpec);
        if (!$this->has($part)) {
            throw new Exception\RuntimeException(
                sprintf("%s: No input found under name '%s'", __METHOD__, $part));
        }
        $obj    = $this->get($part);
        if (($obj instanceof InputInterface) && (empty($inputNameSpec))) {
            //We have found the required input
            return $obj;
        } elseif (($obj instanceof VivoInputFilter) && (!empty($inputNameSpec))) {
            //Recurse into nested input filter
            $input  = $obj->getInputBySpec($inputNameSpec);
            return $input;
        } else {
            //Wrong input specification
            throw new Exception\ConfigException(sprintf("%s: Wrong input specification (%s)", __METHOD__, $part));
        }
    }

    /**
     * Adds a new condition
     * @param ConditionInterface $condition
     */
    public function addCondition(ConditionInterface $condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * Populate the values of all attached inputs and apply conditions
     * @return void
     */
    protected function populate()
    {
        parent::populate();
        $this->applyConditions();
    }
}
