<?php
namespace Vivo\InputFilter\Condition;

/**
 * ConditionAwareInterface
 */
interface ConditionAwareInterface
{
    /**
     * Sets the current value of the condition
     * @param bool $condition
     * @return void
     */
    public function setConditionValue($condition);
}
