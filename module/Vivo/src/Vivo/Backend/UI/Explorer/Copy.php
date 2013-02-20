<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\Backend\UI\Form\Copy as CopyForm;
use Vivo\Form\Form;
use Vivo\CMS\Model\Document;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\Util\RedirectEvent;

/**
 * Copy
 */
class Copy extends AbstractForm
{
    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $documentApi;

    /**
     * Path builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     */
    public function __construct(DocumentApiInterface $documentApi, PathBuilderInterface $pathBuilder)
    {
        $this->documentApi  = $documentApi;
        $this->pathBuilder  = $pathBuilder;
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
            $copiedDoc  = $this->documentApi->copyDocument($doc, $explorer->getSite(), $validData['path'],
                                                   $validData['name_in_path'], $validData['name']);
            $explorer->setEntity($copiedDoc);
            $explorer->setCurrent('editor');
            $this->events->trigger(new RedirectEvent());
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
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        /** @var $doc Document */
        $doc        = $explorer->getEntity();
        $parentPath = $this->pathBuilder->dirname($doc->getPath());
        $basename   = $this->pathBuilder->basename($doc->getPath());
        $form->get('path')->setValue($parentPath);
        $form->get('name')->setValue($doc->getTitle() . ' COPY');
        $form->get('name_in_path')->setValue($basename . '-copy');
        return $form;
    }
}
