<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\UI\AbstractForm;
use Vivo\UI\TabContainerItemInterface;
use Vivo\Backend\UI\Form\ContentEditor as ContentEditorForm;
use Vivo\CMS\Model;
use Vivo\CMS\ComponentResolver;

class Content extends AbstractForm
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;
    /**
     * @var \Vivo\CMS\Model\ContentContainer
     */
    private $contentContainer;
    /**
     * @var \Vivo\CMS\Model\Content
     */
    private $content;

    /**
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\CMS\Api\Document $documentApi
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct(
        \Zend\ServiceManager\ServiceManager $sm,
        \Vivo\CMS\Api\Document $documentApi,
        \Vivo\Metadata\MetadataManager $metadataManager)
    {
        $this->sm = $sm;
        $this->documentApi = $documentApi;
        $this->metadataManager = $metadataManager;
        $this->autoAddCsrf = false;
    }

    /**
     * @param \Vivo\CMS\Model\ContentContainer $contentContainer
     */
    public function setContentContainer(Model\ContentContainer $contentContainer)
    {
        $this->contentContainer = $contentContainer;
    }

    /**
     * @param \Vivo\CMS\Model\Content $content
     */
    public function setContent(Model\Content $content = null)
    {
        $this->content = $content;
    }

    public function init()
    {
        $this->initEdior();

        parent::init();
    }

    protected function initEdior()
    {
        if($this->content) {
            $form = $this->getForm();
            $form->bind($this->content);

            if(!$this->content->getUuid()) {
                $form->get('content')->get('state')->setValue('NEW');
            }

            $resolver = new ComponentResolver($this->sm->get('cms_config'));
            $editorClass = $resolver->resolve(get_class($this->content), ComponentResolver::EDITOR_COMPONENT);

            /* @var $editor \Vivo\CMS\UI\Content\Editor\EditorInterface */
            $editor = $this->sm->create($editorClass);
            $editor->setContent($this->content);

            $this->addComponent($editor, 'contentEditor');
        }
    }

    protected function doGetForm()
    {
        if($this->content) {
            $id = $this->content->getUuid();
            $metadata = $this->metadataManager->getMetadata(get_class($this->content));
        }
        else {
            $id = 0;
            $metadata = array();
        }

        $form = new ContentEditorForm('content-'.$id, $metadata);

        return $form;
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->getForm()->isValid()) {
            if(!$this->contentContainer->getUuid()) {
                $this->contentContainer = $this->documentApi->createContentContainer(
                    $this->getParent('Vivo\Backend\UI\Explorer\Explorer')->getEntity()
                );
            }

            $this->contentEditor->save($this->contentContainer);

            return true;
        }

        return false;
    }

}
