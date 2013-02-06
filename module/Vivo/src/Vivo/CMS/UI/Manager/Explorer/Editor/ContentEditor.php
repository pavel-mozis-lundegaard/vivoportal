<?php
namespace Vivo\CMS\UI\Manager\Explorer\Editor;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\EntityEditor as EntityEditorForm;

class ContentEditor extends AbstractForm
{
    /**
     * @var array
     */
    private $contents;

    private $entity;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct(array $contents, $metadataManager)
    {
        $this->contents = $contents;
        $this->metadataManager = $metadataManager;
    }

    public function init()
    {
        foreach ($this->contents as $content) {
            if($content->getState() == 'PUBLISHED') {
                $this->entity = $content;
                break;
            }
        }

        $this->getForm()->bind($this->entity);

        parent::init();
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));

        $form = new EntityEditorForm('content', $metadata);

        return $form;
    }

    public function save()
    {
        echo __METHOD__."<br>\n";
//         $form = $this->getForm();

//         if ($form->isValid()) {

//         }
    }
}