<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Api\CMS;
use Vivo\Backend\UI\Form\Delete as DeleteForm;
use Vivo\Form\Form;
use Vivo\Util\RedirectEvent;

/**
 * Delete
 */
class Delete extends AbstractForm
{
    /**
     * CMS API
     * @var CMS
     */
    protected $cmsApi;

    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $docApi;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\DocumentInterface $docApi
     */
    public function __construct(CMS $cmsApi, DocumentApiInterface $docApi)
    {
        $this->cmsApi   = $cmsApi;
        $this->docApi   = $docApi;
    }

    public function view()
    {
        /** @var $explorer Explorer */
        $explorer                   = $this->getParent();
        $this->getView()->entity    = $explorer->getEntity();
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
            $docParent  = $this->cmsApi->getParent($doc);
            $this->cmsApi->removeEntity($doc);
            $explorer->setEntity($docParent);
            $explorer->setCurrent('viewer');
            $this->events->trigger(new RedirectEvent());
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
        $this->events->trigger(new RedirectEvent());
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
        $hasSubdocs = $this->docApi->hasChildDocuments($explorer->getEntity());
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
