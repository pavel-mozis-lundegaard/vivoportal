<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Backend\UI\Form\EntityEditor as EntityEditorForm;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\ContentContainer;

use Zend\EventManager\Event;

class Editor extends AbstractForm
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;

    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $documentApi;

    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     * @var \Vivo\UI\Alert
     */
    private $alert;

    /**
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     */
    public function __construct($sm, \Vivo\Metadata\MetadataManager $metadataManager, DocumentApiInterface $documentApi)
    {
        $this->sm               = $sm;
        $this->metadataManager  = $metadataManager;
        $this->documentApi      = $documentApi;
    }

    /**
     * @param Alert $alert
     */
    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function init()
    {
        $this->getParent()->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));

        $this->entity = $this->getParent()->getEntity();

        $this->initEdior();
    }

    private function initEdior()
    {
        $this->contentTab->removeAllComponents();

        $this->getForm()->bind($this->entity);

        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        $containers = $this->documentApi->getContentContainers($this->entity);
        $count = count($containers);
        foreach ($containers as $index => $contentContainer) {
            $this->contentTab->addComponent($this->createContentTab($contentContainer), "content_$index");
        }

        $this->contentTab->addComponent($this->createContentTab(new ContentContainer()), 'content_'.++$count);

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
    private function createContentTab(\Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $e = $this->sm->create('Vivo\Backend\UI\Explorer\Editor\ContentTab');
        $e->setContentContainer($contentContainer);

        return $e;
    }

    /**
     * Save action.
     * @throws \Exception
     */
    public function save()
    {
        $success = true;
        $form = $this->getForm();

        if ($form->isValid()) {
            $this->entity = $this->documentApi->saveDocument($this->entity);
        }
        else {
            $success = false;
        }

        /* @var $component \Vivo\Backend\UI\Explorer\Editor\ContentTab */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        try {
            $success &= $component->save();
        }
        catch(\Exception $e) {
            throw $e;
        }

        if($this->alert) {
            if($success) {
                $this->alert->addMessage('Saved...', Alert::TYPE_SUCCESS);
            }
            else {
                $this->alert->addMessage('Error...', Alert::TYPE_ERROR);
            }
        }

//         if($this->success) {
//             $this->redirector->redirect();
//         }
    }
}
