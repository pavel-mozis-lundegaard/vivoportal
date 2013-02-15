<?php
namespace Vivo\Backend\UI;

use Vivo\Security\Manager\AbstractManager;

class Logon extends \Vivo\UI\AbstractForm
{

    /**
     * @var AbstractManager
     */
    protected $securityManager;

    public function __construct(/*AbstractManager $securityManager*/)
    {
//        $this->securityManager = $securityManager;
    }

    public function logon()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            echo "valid";
        }
    }

    public function logoff()
    {
        echo __METHOD__;
    }

    protected function doGetForm()
    {
        $form = new \Vivo\Form\Logon();

        $fieldset = $form->get('logon');
        /* @var $fieldset \Zend\Form\Fieldset */
        $fieldset->add(array(
            'name'      => 'domain',
            'options'   => array(
                'label'     => 'Domain',
            ),
            'attributes'    => array(
                'type'      => 'text',

            ),
        ));

        $form->add(array(
            'name' => 'act',
            'attributes'    => array(
                'type'          => 'hidden',
                'value'     => $this->getPath('logon'),
            ),
        ));

echo get_class($form->getInputFilter()->get('logon'));
        $form->getInputFilter()->get('logon')->add(array(
              // array(
                'required'  => true,
            //)
            ),'domainx');

    print_r($form->getInputFilter());

    print_r($form->getInputFilter()->get('logon'));
    die();


        return $form;
    }
}
