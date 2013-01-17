<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\EntityEditor as EntityEditorForm;

class Editor extends AbstractForm
{
    private $explorer;

    private $metadataManager;

    private $entity;

    public function __construct($explorer, $metadataManager)
    {
        $this->explorer = $explorer;
        $this->metadataManager = $metadataManager;
    }

    public function init()
    {
        $this->entity = $this->explorer->getEntity();

        $form = $this->getForm();
        $form->bind($this->entity);
        $form->prepare();

        $this->getView()->form = $form;
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata($this->entity);

        $form = new EntityEditorForm('entity-editor', $metadata);

        return $form;
    }
}