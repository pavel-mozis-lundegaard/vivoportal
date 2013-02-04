<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\EntityEditor as EntityEditorForm;

class Editor extends AbstractForm
{
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;
    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct($metadataManager)
    {
        $this->metadataManager = $metadataManager;
    }

    public function init()
    {
        $this->entity = $this->getParent()->getEntity();

        $this->getForm()->bind($this->entity);

        parent::init();
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));
        $action = $this->request->getUri()->getPath();

        $form = new EntityEditorForm('entity-editor', $metadata);
        $form->setAttribute('action', $action);
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));

        return $form;
    }

    public function save()
    {
        $form = $this->getForm();

        if ($form->isValid()) {
//             print_r($this->entity);

//             $this->redirector->redirect();
        }
    }
}