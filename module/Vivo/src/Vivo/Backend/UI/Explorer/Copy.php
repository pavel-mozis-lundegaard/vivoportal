<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\CMS;
use Vivo\Backend\UI\Form\Copy as CopyForm;
use Vivo\Form\Form;

/**
 * Copy
 */
class Copy extends AbstractForm
{
    /**
     * CMS Api
     * @var CMS
     */
    protected $cmsApi;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     */
    public function __construct(CMS $cmsApi)
    {
        $this->cmsApi  = $cmsApi;
    }

    public function view()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $this->getView()->entity = $explorer->getEntity();
        return parent::view();
    }

    /**
     * Copy action
     */
    public function copy()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            $validData  = $form->getData();
            /** @var $explorer Explorer */
            $explorer   = $this->getParent();
            //Copy - and redirect
            $doc        = $explorer->getEntity();
            $copiedDoc  = $this->cmsApi->copyDocument($doc, $explorer->getSite(), $validData['path'],
                                                   $validData['name_in_path'], $validData['name']);
            $explorer->setEntity($copiedDoc);
            $explorer->setCurrent('editor');
//            $this->redirector->redirect();
        }
    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return Form
     */
    protected function doGetForm()
    {
        $form   = new CopyForm();
        $form->setAttribute('action', $this->request->getUri()->getPath());
        $form->add(array(
            'name'  => 'act',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->getPath('copy'),
            ),
        ));
        return $form;
    }
}
