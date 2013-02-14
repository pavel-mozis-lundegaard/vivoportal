<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\UI\AbstractForm;
use Vivo\UI\TabContainerItemInterface;
use Vivo\Backend\UI\Form\ContentEditor as ContentEditorForm;
use Vivo\CMS\ComponentResolver;

class Content extends AbstractForm
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;
    /**
     * @var \Vivo\CMS\Model\ContentContainer
     */
    private $contentContainer;
    /**
     * @var array
     */
    private $contents = array();
    /**
     * @var \Vivo\CMS\Model\Content
     */
    private $content;
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

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
    public function setContentContainer(\Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $this->contentContainer = $contentContainer;
    }

    /**
     * @param \Vivo\CMS\Model\Content $content
     */
    public function setContent(\Vivo\CMS\Model\Content $content = null)
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
            $this->getForm()->bind($this->content);

            $resolver = new ComponentResolver($this->sm->get('cms_config'));
            $editorClass = $resolver->resolve(get_class($this->content), ComponentResolver::EDITOR_COMPONENT);

            /* @var $editor \Vivo\CMS\UI\Content\Editor\EditorInterface */
            $editor = $this->sm->create($editorClass);
            $editor->setContent($this->content);

            if($editor instanceof \Vivo\Service\Initializer\RequestAwareInterface) {
                $editor->setRequest($this->request);
            }

            $this->addComponent($editor, 'contentEditor');
        }
    }

    protected function doGetForm()
    {
        if($this->content) {
            $metadata = $this->metadataManager->getMetadata(get_class($this->content));

            $form = new ContentEditorForm('content-'.$this->content->getUuid(), $metadata);
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
            $this->documentApi->saveContent($this->contentContainer, $this->content);

            return true;
        }

        return false;
    }

}
