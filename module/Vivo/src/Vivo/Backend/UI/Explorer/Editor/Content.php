<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\CMS\UI\AbstractForm;
use Vivo\Backend\UI\Form\ContentEditor as ContentEditorForm;
use Vivo\UI\TabContainerItemInterface;

class Content extends AbstractForm
{
    /**
     * @var \Vivo\CMS\Model\ContentContainer
     */
    private $contentContainer;
    /**
     * @var array
     */
    private $contents = array();
    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @param \Vivo\CMS\Api\Document $documentApi
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\CMS\Model\ContentContainer $contentContainer
     */
    public function __construct(
        \Vivo\CMS\Api\Document $documentApi,
        \Vivo\Metadata\MetadataManager $metadataManager,
        \Vivo\CMS\Model\ContentContainer $contentContainer, $e)
    {
        $this->documentApi = $documentApi;
        $this->metadataManager = $metadataManager;
        $this->contentContainer = $contentContainer;
        $this->entity = $e;
        $this->autoAddCsrf = false;
    }

    public function init()
    {
        $this->initEdior();

        parent::init();
    }

    protected function initEdior()
    {
        if($this->entity) {
            $this->getForm()->bind($this->entity);

            // Example entity editor
            switch (get_class($this->entity)) {
                case 'Vivo\CMS\Model\Content\File':
                    $editorClass = 'Vivo\CMS\UI\Content\Editor\File';
                    break;

                case 'Vivo\CMS\Model\Content\Overview':
                    $editorClass = 'Vivo\CMS\UI\Content\Editor\Overview';
                    break;

                default:
                    $editorClass = 'Vivo\CMS\UI\Content\Editor\Editor';
                    break;
            }

            $editor = new $editorClass;
            if($editor instanceof \Vivo\Service\Initializer\RequestAwareInterface) {
                $editor->setRequest($this->request);
            }
            $this->addComponent($editor, 'contentEditor');
        }
    }

    protected function doGetForm()
    {
        if($this->entity) {
            $metadata = $this->metadataManager->getMetadata(get_class($this->entity));

            $form = new ContentEditorForm('content-'.$this->entity->getUuid(), $metadata);
        }
        else {
            $form = new ContentEditorForm('content-NEW');
        }

        return $form;
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->getForm()->isValid()) {
            $this->contentEditor->save();
            $this->documentApi->saveContent($this->contentContainer, $this->entity);

            return true;
        }

        return false;
    }

}
