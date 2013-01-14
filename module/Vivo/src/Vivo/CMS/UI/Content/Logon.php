<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI\AbstractForm;
use Vivo\Form\Logon as ZfFormLogon;

use Zend\Form\Form as ZfForm;

/**
 * Logon UI component
 * UI for client authentication
 */
class Logon extends AbstractForm
{
    public function init()
    {
        $this->view->form   = $this->form;
    }

    /**
     * Submit action
     */
    public function submit() {
        $this->loadFromRequest();
        if ($this->form->isValid()) {
            //Form is valid
            $validatedData  = $this->form->getData();
            //TODO - Log the user in and redirect
            die (sprintf("Login not implemented. (Username = '%s', Password = '%s')",
                         $validatedData['logon']['username'], $validatedData['logon']['password']));
        }
    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return ZfForm
     */
    protected function createForm()
    {
        $form   = new ZfFormLogon();
        return $form;
    }
}
