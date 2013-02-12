<?php
namespace Vivo\CMS\UI\Manager\Explorer\Editor;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\ContentEditor as ContentEditorForm;
use Vivo\UI\TabContainerItemInterface;

class Content extends AbstractForm implements TabContainerItemInterface
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
        \Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $this->documentApi = $documentApi;
        $this->metadataManager = $metadataManager;
        $this->contentContainer = $contentContainer;
        $this->autoAddCsrf = false;
    }

    public function init()
    {
        $this->initEdior();

        parent::init();
    }

    protected function initEdior()
    {
        try {
            $this->contents = $this->documentApi->getContents($this->contentContainer);
        }
        catch(\Exception $e) {
            $this->contents = array();
        }

        if($this->contents) {
            foreach ($this->contents as $content) {
//             echo $content->getUuid()." - " .$content->getPath()."\n";

                if($content->getState() == 'PUBLISHED') {
                    $this->entity = $content;
                    break;
                }
            }
        }

        if($this->entity == null && count($this->contents)) {
            $this->entity = $this->contents[0];
        }

        if($this->entity) {
            $this->getForm()->bind($this->entity);
        }
    }

    protected function doGetForm()
    {
        if($this->entity) {
            $metadata = $this->metadataManager->getMetadata(get_class($this->entity));

            $form = new ContentEditorForm('content-'.$this->entity->getUuid(), $this->contents, $metadata);
        }
        else {
            $form = new ContentEditorForm('content-NEW', array(), array());
        }

        return $form;
    }

    public function changeVersion()
    {
        $version = $this->getForm()->get('version')->getValue();

        list($type, $param) = explode(':', $version);

        if($type == 'NEW') {
            $this->entity = new $param;
            $this->getForm()->bind($this->entity);
        }
        elseif($type == 'EDIT') {

        }
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->getForm()->isValid()) {
            $this->documentApi->saveContent($this->entity);

            return true;
        }

        return false;
    }

    public function select()
    {
        // TODO: Auto-generated method stub
    }

    public function isDisabled()
    {
        return false;
    }

    public function getLabel()
    {
        return $this->contentContainer->getContainerName() ? $this->contentContainer->getContainerName() : '+';
    }

}
