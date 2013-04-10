<?php
namespace Vivo\InputFilter\Condition;

/**
 * ConditionInterface
 * InputFilter condition interface
 */
interface ConditionInterface
{
    /**
     * Returns condition value
     * @param array $data
     * @return boolean
     */
    public function getConditionValue(array $data);

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
    public function getConditionalValidators();
}
