<?php
namespace Vivo\Backend\Form\Element;

use Vivo\Form\Element\Select;

class ResourceSelect extends Select
{
    /**
     * Create an empty option (option with label but no value). If set to null, no option is created
     * @var string|null
     */
    protected $emptyOption  = 'Select resource';

    /**
     * Provide default input rules for this element
     *
     * Attaches the captcha as a validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = array(
            'name'          => $this->getName(),
            'required'      => true,
            'allow_empty'   => true,
            'validators'    => array(
                $this->getValidator()
            )
        );
        return $spec;
    }
}
