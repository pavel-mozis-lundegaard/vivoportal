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
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct($cms, $metadataManager)
    {
        $this->cms = $cms;
        $this->metadataManager = $metadataManager;
    }

    public function init()
    {
        $this->getParent()->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));

        $this->entity = $this->getParent()->getEntity();

        $this->getForm()->bind($this->entity);

        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        foreach ($this->cms->getContentContainers($this->entity) as $index => $contentContainer) {
//             echo 'content'.$index."\n";
            $this->contentTab->addComponent($this->getContentForm($contentContainer), "content_$index");
        }

        parent::init();
    }
    /**
     * Callback for entity change event.
     * @param Event $e
     */
    public function onEntityChange(Event $e)
    {
        $this->entity = $e->getParam('entity');
    }

    public function setTabContainer(\Vivo\UI\TabContainer $tab)
    {
        $this->addComponent($tab, 'contentTab');
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));
        $action = $this->request->getUri()->getPath();

        $form = new EntityEditorForm('document', $metadata);
        $form->setAttribute('action', $action);
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
            ),
        ));

        return $form;
    }

    protected function getContentForm($contentContainer)
    {
        $contents = $this->cms->getContents($contentContainer);

        $e = new Editor\ContentEditor($contents, $this->metadataManager);
        $e->setRequest($this->request);
        $e->setView(new \Zend\View\Model\ViewModel());
        $e->init();

        return $e;
    }

    public function save()
    {
        $form = $this->getForm();

        if ($form->isValid()) {
//             print_r($this->entity);

//             $this->redirector->redirect();
        }

        /* @var $tab \Vivo\CMS\UI\Manager\Explorer\Editor\ContentEditor */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        if(!$component instanceof Editor\ContentEditor)
        {
            throw new \Exception('Selected tab is not instance of Vivo\CMS\UI\Manager\Explorer\Editor\ContentEditor');
        }

        $component->save();
    }
}