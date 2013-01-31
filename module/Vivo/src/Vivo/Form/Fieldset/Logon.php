<?php
namespace Vivo\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Logon
 * Logon fieldset
 */
class Logon extends Fieldset implements InputFilterProviderInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('logon');

        $this->setLabel('User credentials');

        //Username
        $this->add(array(
            'name'      => 'username',
            'options'   => array(
                'label'     => 'Username',
            ),
            'attributes'    => array(
                'type'          => 'text',
            ),
        ));

        //Password
        $this->add(array(
            'name'      => 'password',
            'type'      => 'Zend\Form\Element\Password',
            'options'   => array(
                'label'     => 'Password',
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'username'  => array(
                'required'  => true,
            ),
            'password'  => array(
                'required'  => true,
            ),
        );
    }
}
