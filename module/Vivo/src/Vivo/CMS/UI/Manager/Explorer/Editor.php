<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\EntityEditor as EntityEditorForm;
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
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct(\Vivo\CMS\Api\CMS $cms, \Vivo\Metadata\MetadataManager $metadataManager)
    {
        $this->cms = $cms;
        $this->metadataManager = $metadataManager;
        $this->csrfTimeout = 3600;
    }

    public function init()
    {
        $this->getParent()->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));

        $this->entity = $this->getParent()->getEntity();

        $this->initEdior();

        parent::init();
    }

    protected function initEdior()
    {
        $this->getForm()->bind($this->entity);

        $this->contentTab->removeAllComponents();

        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        foreach ($this->cms->getContentContainers($this->entity) as $index => $contentContainer) {
            $this->contentTab->addComponent($this->getContentForm($contentContainer), "content_$index");
        }
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
     * @return \Vivo\CMS\UI\Manager\Explorer\Editor\ContentEditor
     */
    protected function getContentForm(\Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $e = new Editor\ContentEditor($this->cms, $this->metadataManager, $contentContainer);
        $e->setRequest($this->request);
        $e->setView(new \Zend\View\Model\ViewModel());

        return $e;
    }

    /**
     * Save action.
     * @throws \Exception
     */
    public function save()
    {
        $result = true;
        $form = $this->getForm();

        if ($form->isValid()) {
            $this->cms->saveDocument($this->entity);
        }
        else {
            $result = false;
        }

        /* @var $tab \Vivo\CMS\UI\Manager\Explorer\Editor\ContentEditor */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        if(!$component instanceof Editor\ContentEditor) {
            throw new \Exception('Selected tab is not instance of Vivo\CMS\UI\Manager\Explorer\Editor\ContentEditor');
        }

        $result &= $component->save();

        if($result) {
//             echo 'ok';
            $this->redirector->redirect();
        }
    }
}
