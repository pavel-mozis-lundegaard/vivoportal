<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\Model\Document;
use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\UI\Manager\Form\Delete as DeleteForm;
use Vivo\Form\Form;

/**
 * Delete
 */
class Delete extends AbstractForm
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
        $this->cms      = $cms;
    }

    public function view()
    {
        /** @var $explorer Explorer */
        $explorer                   = $this->getParent();
        $this->getView()->entity = $explorer->getEntity();
        return parent::view();
    }

    /**
     * Delete action
     */
    public function delete()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            /** @var $explorer Explorer */
            $explorer   = $this->getParent();
            //Delete - and redirect
            $doc        = $explorer->getEntity();
            $docParent  = $this->cms->getParent($doc);
            $this->cms->removeDocument($doc);
            $explorer->setEntity($docParent);
            $explorer->setCurrent('viewer');
            $explorer->saveState();
            $this->redirector->redirect();
        }
    }

    /**
     * Cancel action
     * Redirects to viewer
     */
    public function cancel()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $explorer->setCurrent('viewer');
        $explorer->saveState();
        $this->redirector->redirect();
    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return Form
     */
    protected function doGetForm()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $hasSubdocs = $this->cms->hasChildDocuments($explorer->getEntity());
        $form       = new DeleteForm($hasSubdocs);
        $form->setAttribute('action', $this->request->getUri()->getPath());
        $form->add(array(
            'name'  => 'act',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->getPath('delete'),
            ),
        ));
        return $form;
    }
}
