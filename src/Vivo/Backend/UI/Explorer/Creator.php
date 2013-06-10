<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\Alert;
use Vivo\Form\Fieldset;
use Vivo\Util\RedirectEvent;
use Vivo\CMS\Model\Folder;

use Zend\ServiceManager\ServiceManager;

/**
 * Creator
 */
class Creator extends Editor
{
    /**
     * @var \Vivo\Backend\UI\Explorer\ExplorerInterface
     */
    private $explorer;

    /**
     * Parent folder for the entity being created
     * @var Folder
     */
    protected $parentFolder;

    public function init()
    {
        $this->explorer = $this->getParent('Vivo\Backend\UI\Explorer\ExplorerInterface');
        $this->parentFolder = $this->explorer->getEntity();
        $this->doCreate();

        $this->initForm();

        $form   = $this->getForm();
    }

    public function create()
    {
    }

    /**
     * Returns path which should be passed to availableContentsProvider
     * @return string
     */
    protected function getPathForContentsProvider()
    {
        return $this->parentFolder->getPath();
    }

    protected function doGetForm()
    {
        $form = parent::doGetForm();
        $form->add(array(
            'name' => '__type',
            'type' => 'Vivo\Form\Element\Select',
            'attributes' => array(
                'options' => array(
                    'Vivo\CMS\Model\Document' => 'Vivo\CMS\Model\Document',
                    'Vivo\CMS\Model\Folder' => 'Vivo\CMS\Model\Folder',
                )
            )
        ));
        return $form;
    }

    private function doCreate()
    {
        $entityClass = $this->request->getPost('__type', 'Vivo\CMS\Model\Document');
        $this->entity = new $entityClass;
        $this->resetForm();
        $this->getForm()->bind($this->entity);
        $this->getForm()->get('__type')->setValue($entityClass);
        $this->loadFromRequest();
    }

    public function save()
    {
        $sessionName    = $this->getForm()->get('csrf')->getCsrfValidator()->getSessionName();
        \Zend\Debug\Debug::dump($sessionName, 'Session name');
        \Zend\Debug\Debug::dump($this->getCsrfHashFromSession($sessionName), 'CSRF Hash from session');
        die('SAVE');

        if($this->getForm()->isValid()) {
            $parent = $this->explorer->getEntity();
            $this->entity = $this->documentApi->createDocument($parent, $this->entity);
            $this->explorer->setEntity($this->entity);
            $this->saveProcess();
            $this->explorer->setCurrent('editor');
            //$this->events->trigger(new RedirectEvent());
            $this->addAlertMessage('Created...', Alert::TYPE_SUCCESS);
            echo 'OK';
        }
        else {
            $this->addAlertMessage('Error...', Alert::TYPE_ERROR);
            echo 'ERROR';
        }
        //DEBUG
        $this->debug();
        die('SAVE');
    }

    protected function debug()
    {
        $form           = $this->getForm();
        $inputF         = $form->getInputFilter();
        $csrfField      = $form->get('csrf');
        /** @var $csrfValidator \Zend\Validator\Csrf */
        $csrfValidator  = $csrfField->getCsrfValidator();
        $session        = $csrfValidator->getSession();
        \Zend\Debug\Debug::dump($session->getName(), 'Session name');
        \Zend\Debug\Debug::dump($csrfValidator->getName(), 'CSRF Validator name');
        echo 'Session vars';
        foreach ($session as $key => $value) {
            \Zend\Debug\Debug::dump($value, $key);
        }
        \Zend\Debug\Debug::dump($inputF->getRawValue('csrf'), 'Csrf RAW');
        \Zend\Debug\Debug::dump($csrfValidator->getHash(), 'Validator hash');
        \Zend\Debug\Debug::dump($inputF->getRawValues(), 'All RAW values');
        \Zend\Debug\Debug::dump($form->getMessages(), 'Form error messages');
        die('DEBUG');
    }

    protected function getCsrfHashFromSession($sessionName)
    {
        $session    = new \Zend\Session\Container($sessionName);
        if (isset($session['hash'])) {
            $hash = $session['hash'];
        } else {
            $hash   = null;
        }
        return $hash;
    }
}
