<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Exception;

use Zend\Stdlib\ArrayUtils;
use Zend\Validator\NotEmpty as ZfNotEmpty;

use Traversable;

/**
 * AllEmpty
 * This condition evaluates to true when all specified fields are empty
 */
class AllEmpty extends AbstractCondition
{
    /**
     * Array of fields which are checked for being empty
     * array(
     *      array(
     *          'field'     => string|array,    //field reference - for field in the current fieldset use string,
     *                                          //otherwise an array
     *          'emptyType' => integer|array,   //one or more of the Zend\Validator\NotEmpty constants
     *                                          //describing empty type
     *      ),
     * )
     * @var array
     */
    protected $fields   = array();

    /**
     * Default empty type used when 'emptyType' is not specified with a definition
     * If not set, uses Zend\Validator\NotEmpty default
     * @var integer|array
     */
    protected $defaultEmptyType;

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
            if (array_key_exists('fields', $options)) {
                $this->setFields($options['fields']);
            }
            if (array_key_exists('defaultEmptyType', $options)) {
                $this->setDefaultEmptyType($options['defaultEmptyType']);
            }
        }
        parent::__construct($options);
    }

    /**
     * Sets fields which are checked for being empty
     * @param array $fields
     * @throws \Vivo\InputFilter\Exception\InvalidArgumentException
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            if (!isset($field['field'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: 'field' key missing in field definition with key '%s'", __METHOD__, $key));
            }
            if (is_string($field['field'])) {
                $fields[$key]['field']  = array($field['field']);
            }
            if (!isset($field['emptyType'])) {
                $fields[$key]['emptyType']  = $this->defaultEmptyType;
            }
        }
        $this->fields   = $fields;
    }

    /**
     * Sets the default empty type used when emptyType is not specified with field definition
     * @param integer|array $type
     * @throws \Vivo\InputFilter\Exception\InvalidArgumentException
     */
    public function setDefaultEmptyType($type)
    {
        $this->checkEmptyType($type);
        $this->defaultEmptyType = $type;
    }

    /**
     * Checks format of the empty type and throws an exception when invalid
     * @param integer|array|null $type
     * @throws \Vivo\InputFilter\Exception\InvalidArgumentException
     */
    protected function checkEmptyType($type)
    {
        if (!is_null($type) && !is_int($type) && !is_array($type)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Invalid Empty Type format; must be specified either as an integer or an array",
                    __METHOD__));
        }
    }

    /**
     * Returns condition value
     * Returns true when all specified fields are empty
     * @param array $data
     * @return boolean
     */
    public function getConditionValue(array $data)
    {
        $allEmpty   = true;
        foreach ($this->fields as $fieldDesc) {
            $notEmptyValidator  = new ZfNotEmpty();
            if (!is_null($fieldDesc['emptyType'])) {
                $notEmptyValidator->setType($fieldDesc['emptyType']);
            }
            $fieldValue         = $this->getFieldValue($fieldDesc['field'], $data);
            $notEmpty           = $notEmptyValidator->isValid($fieldValue);
            if ($notEmpty) {
                $allEmpty   = false;
                break;
            }
        }
        return $allEmpty;
    }
}
