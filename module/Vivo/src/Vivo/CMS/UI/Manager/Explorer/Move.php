<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\CMS;

use Zend\Form\Form as ZfForm;

/**
 * Move
 */
class Move extends AbstractForm
{
    /**
     * CMS Api
     * @var CMS
     */
    protected $cms;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cms
     */
    public function __construct(CMS $cms)
    {
        $this->cms  = $cms;
    }

    public function view()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $this->getView()->entity = $explorer->getEntity();
        return parent::view();
    }

    /**
     * Submit action
     */
    public function submit()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            $validData  = $form->getData();
            \Zend\Debug\Debug::dump($validData);
            /** @var $explorer Explorer */
            $explorer   = $this->getParent();
//            if ($validData['yes']) {
                //Delete - and redirect
//                $docParent  = $this->cms->getParent($explorer->getEntity());
//                $this->cms->removeDocument($this->document);
//                $explorer->setEntityByRelPath($docParent->getPath());
//                $explorer->setEntity($docParent);
//            }
            //TODO - set Explorer current to viewer
//            $explorer->setCurrentPublic('viewer');
//            $explorer->saveState();
//            $this->redirector->redirect();

        }

    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return ZfForm
     */
    protected function doGetForm()
    {
        $form   = new ZfForm();
        $form->setAttribute('action', $this->request->getUri()->getPath());
        $form->add(array(
            'name'  => 'act',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->getPath('submit'),
            ),
        ));
        $form->add(array(
            'name'          => 'path',
            'attributes'    => array(
                'type'          => 'text',
            ),
            'options'       => array(
                'label'         => 'Path',
            ),
        ));
        $form->add(array(
            'name'          => 'name',
            'attributes'    => array(
                'type'          => 'text',
            ),
            'options'       => array(
                'label'         => 'Name',
            ),
        ));
        $form->add(array(
            'name'          => 'name_in_path',
            'attributes'    => array(
                'type'          => 'text',
            ),
            'options'       => array(
                'label'         => 'Name in path',
            ),
        ));
        $form->add(array(
            'name'          => 'create_hyperlink',
            'type'          => 'Zend\Form\Element\Checkbox',
            'options'       => array(
                'label'         => 'Create hyperlink',
            ),
        ));
        $form->add(array(
            'name'  => 'submit',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes'   => array(
                'value'     => 'Submit',
            ),
        ));
        return $form;
    }
}
