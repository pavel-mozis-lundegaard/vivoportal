<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\Model\Site;
use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\UI\Manager\Form\Copy as CopyForm;

use Zend\Form\Form as ZfForm;

/**
 * Copy
 */
class Copy extends AbstractForm
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
            $copiedDoc  = $this->cms->copyDocument($doc, $explorer->getSite(), $validData['path'],
                                                   $validData['name_in_path'], $validData['name']);
//            $explorer->setEntity($copiedDoc);
            $explorer->setCurrent('viewer');
            $explorer->saveState();
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
