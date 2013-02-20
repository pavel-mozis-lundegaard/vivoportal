<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\Backend\UI\Form\Move as MoveForm;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\CMS\Model\Document;
use Vivo\Util\RedirectEvent;

/**
 * Move
 */
class Move extends AbstractForm
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
    protected $documentApi;

    /**
     * Path Builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     */
    public function __construct(CMS $cmsApi, DocumentApiInterface $documentApi, PathBuilderInterface $pathBuilder)
    {
        $this->cmsApi       = $cmsApi;
        $this->documentApi  = $documentApi;
        $this->pathBuilder  = $pathBuilder;
    }

    public function view()
    {
        /** @var $explorer Explorer */
        $explorer   = $this->getParent();
        $entity     = $explorer->getEntity();
        $this->getView()->entity = $entity;
        $this->getView()->entityRelPath = $this->cmsApi->getEntityRelPath($entity);
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
            $this->events->trigger(new RedirectEvent());
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
        $relPath    = $this->cmsApi->getEntityRelPath($doc);
        $path       = $this->pathBuilder->dirname($relPath);

        $form->get('path')->setValue($path);
        $form->get('name')->setValue($doc->getTitle());
        return $form;
    }
}
