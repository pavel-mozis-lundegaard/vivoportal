<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\Backend\UI\Form\Move as MoveForm;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\CMS\Model\Document;

/**
 * Move
 */
class Move extends AbstractForm
{
    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $documentApi;

    /**
     * Path Builder
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
     * Move action
     */
    public function move()
    {
        $form   = $this->getForm();
        if ($form->isValid()) {
            $validData  = $form->getData();
            /** @var $explorer Explorer */
            $explorer   = $this->getParent();
            //Move - and redirect
            $doc        = $explorer->getEntity();
            $movedDoc   = $this->documentApi->moveDocument($doc, $explorer->getSite(), $validData['path'],
                $validData['name_in_path'], $validData['name'], (bool) $validData['create_hyperlink']);
            $explorer->setEntity($movedDoc);
            $explorer->setCurrent('editor');
//            $this->redirector->redirect();
        }

    }

    /**
     * Creates form and returns it
     * Factory method
     * @return MoveForm
     */
    protected function doGetForm()
    {
        $form   = new MoveForm();
        $form->setAttribute('action', $this->request->getUri()->getPath());
        $form->add(array(
            'name'  => 'act',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->getPath('move'),
            ),
        ));
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        /** @var $doc Document */
        $doc        = $explorer->getEntity();
        $parentPath = $this->pathBuilder->dirname($doc->getPath());
        $form->get('path')->setValue($parentPath);
        $form->get('name')->setValue($doc->getTitle());
        return $form;
    }
}
