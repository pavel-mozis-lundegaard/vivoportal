<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Backend\UI\Form\EntityEditor as EntityEditorForm;
use Vivo\CMS\AvailableContentsProvider;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\ContentContainer;

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
    private $documentApi;

    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @var \Vivo\CMS\AvailableContentsProvider
     */
    private $availableContentsProvider;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     * @var array
     */
    private $availableContents = array();

    /**
     * @var \Vivo\UI\Alert
     */
    private $alert;

    /**
     * @var boolean
     */
    protected $autoAddCsrf = false;

    /**
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     */
    public function __construct(
        \Zend\ServiceManager\ServiceManager $sm,
        \Vivo\Metadata\MetadataManager $metadataManager,
        DocumentApiInterface $documentApi,
        AvailableContentsProvider $availableContentsProvider)
    {
        $this->sm = $sm;
        $this->metadataManager = $metadataManager;
        $this->documentApi = $documentApi;
        $this->availableContentsProvider = $availableContentsProvider;
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
        $this->entity = $this->getParent()->getEntity();

        $this->getForm()->bind($this->entity);

        parent::init();

        // Load avalilable contents
        $this->availableContents = $this->availableContentsProvider->getAvailableContents($this->entity);

        // Editor tabs
        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        $containers = $this->documentApi->getContentContainers($this->entity);
        $count = count($containers);
        foreach ($containers as $index => $contentContainer) {
            $this->contentTab->addComponent($this->createContentTab($contentContainer), "content_$index");
        }

        $this->contentTab->addComponent($this->createContentTab(new ContentContainer()), "content_$count");
    }

    /**
     * @param \Vivo\UI\TabContainer $tab
     */
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
     * @return \Vivo\Backend\UI\Explorer\Editor\ContentTab
     */
    private function createContentTab(ContentContainer $contentContainer)
    {
        $tab = $this->sm->create('Vivo\Backend\UI\Explorer\Editor\ContentTab');
        $tab->setContentContainer($contentContainer);
        $tab->setAvailableContents($this->availableContents);

        return $tab;
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
                $this->contentTab->removeAllComponents();
                $this->init();
                $selected = $this->contentTab->getSelectedComponent();
                $selected->initForm();

                $this->alert->addMessage('Saved...', Alert::TYPE_SUCCESS);
            }
            else {
                $this->alert->addMessage('Error...', Alert::TYPE_ERROR);
            }
        }

//         if($this->success) {
//             $this->events->trigger(new RedirectEvent($redirUrl));
//         }
    }
}
