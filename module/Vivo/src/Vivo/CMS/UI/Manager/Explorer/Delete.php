<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;

use Zend\Form\Form as ZfForm;

/**
 * Delete
 */
class Delete extends AbstractForm
{
    public function view()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $this->getView()->entity = $explorer->getEntity();


        return parent::view();
    }

    public function submit()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            $validData  = $form->getData();
            \Zend\Debug\Debug::dump($validData);
            $this->redirector->redirect();
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
        $form->add(array(
            'name'  => 'act',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->getPath(''),
            ),
        ));
        $form->add(array(
            'name'  => 'yes',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes'   => array(
                'value'     => 'Yes',
            ),
        ));
        $form->add(array(
            'name'  => 'no',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes'   => array(
                'value'     => 'No',
            ),
        ));
        return $form;
    }
}
