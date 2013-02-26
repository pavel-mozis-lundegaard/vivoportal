<?php
namespace Vivo\Backend\UI;

use Vivo\Form\DomainLogon;
use Vivo\Security\Manager\AbstractManager;
use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Util\RedirectEvent;

/**
 * Backend logon component
 */
class Logon extends AbstractForm
{
    /**
     * @var AbstractManager
     */
    protected $securityManager;

    /**
     * @var \Vivo\UI\Alert
     */
    private $alert;

    /**
     * Constructor.
     * @param AbstractManager $securityManager
     */
    public function __construct(AbstractManager $securityManager)
    {
        $this->securityManager = $securityManager;
    }

    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Logon action.
     */
    public function logon()
    {
        $form = $this->getForm();
        if ($form->isValid()) {
            $validatedData = $form->getData();
            $result = $this->securityManager->authenticate(
                    $validatedData['logon']['domain'],
                    $validatedData['logon']['username'],
                    $validatedData['logon']['password']
                    );

            if ($result) {
                $this->events->trigger(new RedirectEvent());
            }
            else {
                if($this->alert) {
                    $this->alert->addMessage(
                        'Unable to login (wrong username or password or account is not active or no longer valid)',
                        Alert::TYPE_ERROR
                    );
                }
            }
        }
    }

    /**
     * Logoff action.
     */
    public function logoff()
    {
        $this->securityManager->removeUserPrincipal();
        $this->events->trigger(new RedirectEvent());

        if($this->alert) {
            $this->alert->addMessage('You have been logged out of the system', Alert::TYPE_SUCCESS);
        }
    }

    /**
     * Creates form.
     * @return DomainLogon
     */
    protected function doGetForm()
    {
        $form = new DomainLogon();
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('logon'),
            ),
        ));
        return $form;
    }

    /**
     * Prepare view.
     * @see
     */
    public function view()
    {
        $this->view->user = $this->securityManager->getUserPrincipal();
        return parent::view();
    }
}
