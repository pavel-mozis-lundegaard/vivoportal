<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Exception;

use Zend\Stdlib\ArrayUtils;

use Traversable;

/**
 * NotEmpty
 * Condition returning true when the specified field is not empty
 */
class NotEmpty extends Input
{
    /**
     * Input preset to configure the Input condition to function as NotEmpty condition
     * @var array
     */
    protected $inputPreset  = array(
        'validators'    => array(
            'notEmpty'      => array(
                'name'  => 'notEmpty',
                'options'   => array(
                    //'type'  => \Zend\Validator\NotEmpty::NULL,
                ),
            ),
        ),
    );

    /**
     * Constructor
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options    = array();
        }
        if (isset($options['emptyType'])) {
            $this->inputPreset['validators']['notEmpty']['options']['type'] = $options['emptyType'];
            unset($options['emptyType']);
        }
        $options['inputConfig'] = $this->inputPreset;
        parent::__construct($options);
    }
}
