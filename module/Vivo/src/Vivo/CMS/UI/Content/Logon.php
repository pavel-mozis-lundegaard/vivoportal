<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI\AbstractForm;
use Vivo\Form\Logon as ZfFormLogon;
use Vivo\CMS\Security\AbstractManager as AbstractSecurityManager;
use Vivo\Util\Redirector;

use Zend\Form\Form as ZfForm;

/**
 * Logon UI component
 * UI for client authentication
 */
class Logon extends AbstractForm
{
    /**
     * Security Manager
     * @var AbstractSecurityManager
     */
    protected $securityManager;

    /**
     * Security domain
     * @var string
     */
    protected $securityDomain;

    /**
     * Redirector
     * @var Redirector
     */
    protected $redirector;

    /**
     * Constructor
     * @param AbstractSecurityManager $securityManager
     * @param $securityDomain
     * @param \Vivo\Util\Redirector $redirector
     */
    public function __construct(AbstractSecurityManager $securityManager, $securityDomain, Redirector $redirector, $request)
    {
        $this->securityManager  = $securityManager;
        $this->securityDomain   = $securityDomain;
        $this->redirector       = $redirector;
        //TODO - remove $request from constructor when RequestAware interface is available
        $this->request          = $request;
    }

    public function init()
    {
        $form   = $this->getForm();
        //Prepare the form
        $form->prepare();
        $this->view->form   = $form;
        $this->view->user   = $this->securityManager->getUserPrincipal();
    }

    /**
     * Logon action
     */
    public function logon()
    {
        $this->loadFromRequest();
        $form   = $this->getForm();
        if ($form->isValid()) {
            //Form is valid
            $validatedData  = $form->getData();
            $result = $this->securityManager->authenticate($this->securityDomain,
                                                           $validatedData['logon']['username'],
                                                           $validatedData['logon']['password']);
            /** @var $model \Vivo\CMS\Model\Content\Logon */
            $model      = $this->content;
            $redirUrl   = null;
            if ($result) {
                //Authentication successful
                if ($model->getLogonUrl()) {
                    $redirUrl   = $model->getLogonUrl();
                }
            } else {
                //Authentication failed
                if ($model->getErrorUrl()) {
                    $redirUrl   = $model->getErrorUrl();
                }
            }
            $this->redirector->redirect($redirUrl);
        }
        //Remove password (for security reasons)
        $form->get('logon')->get('password')->setValue('');
    }

    /**
     * Logoff action
     */
    public function logoff()
    {
        $this->securityManager->removeUserPrincipal();
        //TODO - Destroy session?
        /** @var $model \Vivo\CMS\Model\Content\Logon */
        $model      = $this->content;
        $redirUrl   = null;
        if ($model->getLogoffUrl()) {
            $redirUrl   = $model->getLogoffUrl();
        }
        $this->redirector->redirect($redirUrl);
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
