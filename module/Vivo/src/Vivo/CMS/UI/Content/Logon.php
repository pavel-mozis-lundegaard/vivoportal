<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI\AbstractForm;
use Vivo\Form\Logon as ZfFormLogon;
use Vivo\Security\Manager as SecurityManager;

use Zend\Form\Form as ZfForm;

/**
 * Logon UI component
 * UI for client authentication
 */
class Logon extends AbstractForm
{
    /**
     * Security Manager
     * @var SecurityManager
     */
    protected $securityManager;

    /**
     * Constructor
     * @param \Vivo\Security\Manager $securityManager
     */
    public function __construct(SecurityManager $securityManager, $request)
    {
        $this->securityManager  = $securityManager;
        //TODO - remove $request from constructor when RequestAware interface is available
        $this->request          = $request;
    }

    public function init()
    {
        $form   = $this->getForm();
        //Prepare the form
        $form->prepare();
        $this->view->form   = $form;
    }

    /**
     * Submit action
     */
    public function submit() {
        $this->loadFromRequest();
        $form   = $this->getForm();
        if ($form->isValid()) {
            //Form is valid
            $validatedData  = $form->getData();
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
    protected function doGetForm()
    {
        $form   = new ZfFormLogon();
        //Set form name if needed
//        $form->setName('logon_form');
        //Prepend form name to elements if needed
//        $form->setWrapElements(true);
        return $form;
    }
}
