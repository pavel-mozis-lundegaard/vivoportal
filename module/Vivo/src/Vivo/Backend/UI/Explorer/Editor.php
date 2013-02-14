<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\Backend\UI\Form\EntityEditor as EntityEditorForm;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\ContentContainer;

use Zend\EventManager\Event;

class Editor extends AbstractForm
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cms;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;
    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;
    /**
     * @var bool
     */
    private $success;

    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $documentApi;

    /**
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     */
    public function __construct(\Vivo\CMS\Api\CMS $cms, \Vivo\Metadata\MetadataManager $metadataManager, DocumentApiInterface $documentApi)
    {
        $this->cms              = $cms;
        $this->metadataManager  = $metadataManager;
        $this->documentApi      = $documentApi;
    }

    public function init()
    {
        $this->getParent()->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));

        $this->entity = $this->getParent()->getEntity();

        $this->initEdior();
    }

    private function initEdior()
    {
        $this->getForm()->bind($this->entity);

        $this->contentTab->removeAllComponents();

        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        $containers = $this->documentApi->getContentContainers($this->entity);
        $count = count($containers);
        foreach ($containers as $index => $contentContainer) {
            $this->contentTab->addComponent($this->getContentTab($contentContainer), "content_$index");
        }

        $this->contentTab->addComponent($this->getContentTab(new ContentContainer()), 'content_'.++$count);

        parent::init();
    }

    /**
     * Callback for entity change event.
     * @param Event $e
     */
    public function onEntityChange(Event $e)
    {
        $this->entity = $e->getParam('entity');
        $this->initEdior();
    }

    public function setTabContainer(\Vivo\UI\TabContainer $tab)
    {
        $this->addComponent($tab, 'contentTab');
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));
        $action = $this->request->getUri()->getPath();

        $form = new EntityEditorForm('document-'.$this->entity->getUuid(), $metadata);
        $form->setAttribute('action', $action);
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));
        $form->add(array(
            'name' => 'save',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
            ),
        ));

        return $form;
    }

    /**
     * @param \Vivo\CMS\Model\ContentContainer $contentContainer
     * @return \Vivo\Backend\UI\Explorer\Editor\ContentEditor
     */
    private function getContentTab(\Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $e = new Editor\ContentTab($this->documentApi, $this->metadataManager, $contentContainer);
        $e->setRequest($this->request);

        return $e;
    }

    /**
     * Save action.
     * @throws \Exception
     */
    public function save()
    {
        $this->success = true;
        $form = $this->getForm();

        if ($form->isValid()) {
            $this->documentApi->saveDocument($this->entity);
        }
        else {
            $this->success = false;
        }

        /* @var $component \Vivo\Backend\UI\Explorer\Editor\ContentTab */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        if(!$component instanceof Editor\ContentTab) {
            throw new \Exception('Selected tab is not instance of Vivo\Backend\UI\Explorer\Editor\ContentTab');
        }

        try {
            $this->success &= $component->save();
        }
        catch(\Exception $e) {
            throw $e;
        }

//         if($this->success) {
//             $this->redirector->redirect();
//         }
    }

    public function view()
    {
        $this->getView()->success = $this->success;

        return parent::view();
    }
}
