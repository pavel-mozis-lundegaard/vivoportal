<?php
namespace Vivo\Backend\UI;

use Vivo\Security\Manager\AbstractManager;

class Logon extends \Vivo\UI\AbstractForm
{
    protected $securityManager;

    public function __construct(\Vivo\Security\Manager\AbstractManager $securityManager)
    {
        $this->securityManager = $securityManager;
    }

    public function init()
    {
        parent::init();
    }

    public function logon()
    {
        $form = $this->getForm();
        if ($form->isValid()) {
            //Form is valid
            $validatedData = $form->getData();
            $result = $this->securityManager->authenticate(
                    $validatedData['logon']['domain'],
                    $validatedData['logon']['username'],
                    $validatedData['logon']['password']
                    );
        }

        //$this->redirector->redirect();
    }

    public function logoff()
    {
        $this->securityManager->removeUserPrincipal();
    }

    protected function doGetForm()
    {
        $form = new \Vivo\Form\Logon;
        $fs = $form->get('logon');
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('logon'),
            ),
        ));

        $fs->add(array('name' => 'domain',
            'options' => array(
                'label' => 'Domain',
            ),
            'attributes' => array(
                'type' => 'text',
//                'value'         => $this->getPath('logon'),
            ),));

        return $form;
    }

    public function view()
    {
        $this->view->user = $this->securityManager->getUserPrincipal();
        return parent::view();
    }
}
