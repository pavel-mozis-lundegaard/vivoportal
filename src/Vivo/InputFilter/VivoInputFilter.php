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
     * Form conditions
     * @var ConditionInterface[]
     */
    protected $conditions       = array();

    /**
     * 'At least' groups
     * Define groups of fields where at least a predefined number of the fields has to be filled out
     * array(
     *      array(
     *          'group_label'   => 'Name of the group to display in error message',
     *          'fields'        => array(<field names>), //Fields from which at least a certain number has to be filled
     *          'at_least       => 1,                    //At least this number of fields has to be filled
     *          'error_message' => 'error message in case the required number of fields is not filled out'
                                    //Optional. Params: %1$s - group_label, %2$s - at_least
     *      ),
     *      ...,
     * )
     * @var array
     */
    protected $atLeastGroups    = array();

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
     * Adds an 'at least' group
     *      array(
     *          'group_label'   => 'Name of the group to display in error message',
     *          'fields'        => array(<field names>), //Fields from which at least a certain number has to be filled
     *          'at_least       => 1,                    //At least this number of fields has to be filled, default = 1
     *          'error_message' => 'error message in case the required number of fields is not filled out'
     *                              //Optional. Params: %1$s - group_label, %2$s - at_least
     *      ),
     * @param array $atLeastGroup
     * @throws Exception\InvalidArgumentException
     */
    public function addAtLeastGroup(array $atLeastGroup)
    {
        if (!array_key_exists('group_label', $atLeastGroup)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: 'At least' group has to contain the 'group_label' key", __METHOD__));
        }
        if (!array_key_exists('fields', $atLeastGroup)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: 'At least' group '%s' must contain the 'fields' key",
                    __METHOD__, $atLeastGroup['group_label']));
        }
        if (!array_key_exists('at_least', $atLeastGroup)) {
            $atLeastGroup['at_least']   = 1;
        }
        $atLeastGroup['at_least']   = (int) $atLeastGroup['at_least'];
        if ($atLeastGroup['at_least'] < 1) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: 'At least' group '%s' tries to set 'at_least' = %s; minimum is 1",
                    __METHOD__, $atLeastGroup['group_label'], $atLeastGroup['at_least']));
        }
        $numberOfFields             = count($atLeastGroup['fields']);
        if ($atLeastGroup['at_least'] > $numberOfFields) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: 'At least' group '%s' tries to set 'at_least' = %s; maximum is %s (number of fields)",
                    __METHOD__, $atLeastGroup['group_label'], $atLeastGroup['at_least'], $numberOfFields));
        }
        if (!array_key_exists('error_message', $atLeastGroup)) {
            $atLeastGroup['error_message']  = "At least %2\$s field(s) must be filled out in group '%1\$s'";
        }
        $this->atLeastGroups[]  = $atLeastGroup;
    }

    /**
     * Returns 'at least' groups
     * @return array
     */
    public function getAtLeastGroups()
    {
        return $this->atLeastGroups;
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

    /**
     * Is the data set valid?
     * The standard validation is extended by the support for 'at least x of fields filled out' check
     * @throws Exception\RuntimeException
     * @return bool
     */
    public function isValid()
    {
        $isValid        = parent::isValid();
        $atLeastGroups  = $this->getAtLeastGroups();
        $data           = $this->data;
        foreach ($atLeastGroups as $atLeastGroup) {
            //Check the group contains the predefined number of filled fields
            $count      = 0;
            $groupOk    =  false;
            foreach ($atLeastGroup['fields'] as $fieldName) {
                if (array_key_exists($fieldName, $data) && !empty($data[$fieldName])) {
                    $count++;
                    if ($count >= $atLeastGroup['at_least']) {
                        $groupOk    = true;
                        break;
                    }
                }
            }
            if (!$groupOk) {
                //Add an error message to each field in the group
                //TODO - translate the error message and group label
                $translatedErrorMsg     = $atLeastGroup['error_message'];
                $translatedGroupLabel   = $atLeastGroup['group_label'];
                $errorMessage           = sprintf($translatedErrorMsg,
                                                  $translatedGroupLabel, $atLeastGroup['at_least']);
                foreach ($atLeastGroup['fields'] as $fieldName) {
                    /** @var $input \Zend\InputFilter\Input */
                    $input      = $this->get($fieldName);
                    $input->setErrorMessage($errorMessage);
                    unset($this->validInputs[$fieldName]);
                    $this->invalidInputs[$fieldName]    = $input;
                }
                $isValid    = false;
            }
        }
        return $isValid;
    }
}
