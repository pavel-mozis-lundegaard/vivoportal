<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Exception;

use Zend\Stdlib\ArrayUtils;

use Traversable;

/**
 * Equals
 * Condition returning true when the value in the field equals the specified token
 */
class Equals extends Input
{
    /**
     * Input preset to configure the Input condition to function as Identical validator
     * @var array
     */
    protected $inputPreset  = array(
        'validators'    => array(
            'identical'     => array(
                'name'  => 'identical',
                'options'   => array(
                    //'token'   => '',
                ),
            ),
        ),
    );

    /**
     * Constructor
     * @param array|Traversable $options
     * @throws \Vivo\InputFilter\Exception\ConfigException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options    = array();
        }
        if (!isset($options['token'])) {
            throw new Exception\ConfigException(sprintf("%s: Token not set", __METHOD__));
        }
        $this->inputPreset['validators']['identical']['options']['token'] = $options['token'];
        unset($options['token']);
        $options['inputConfig'] = $this->inputPreset;
        parent::__construct($options);
    }
}
