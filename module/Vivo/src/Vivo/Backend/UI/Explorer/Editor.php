<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Form\Fieldset;
use Vivo\Backend\UI\Form\EntityEditor as EntityEditorForm;
use Vivo\CMS\AvailableContentsProvider;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\ContentContainer;
use Vivo\Util\RedirectEvent;
use Vivo\LookupData\LookupDataManager;

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
     * @var LookupDataManager
     */
    protected $lookupDataManager;

    /**
     * @var \Vivo\CMS\AvailableContentsProvider
     */
    private $availableContentsProvider;

    /**
     * @var \Vivo\CMS\Model\Folder
     */
    protected $entity;

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
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\LookupData\LookupDataManager $lookupDataManager
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     * @param \Vivo\CMS\AvailableContentsProvider $availableContentsProvider
     */
    public function __construct(
        \Zend\ServiceManager\ServiceManager $sm,
        \Vivo\Metadata\MetadataManager $metadataManager,
        LookupDataManager $lookupDataManager,
        DocumentApiInterface $documentApi,
        AvailableContentsProvider $availableContentsProvider)
    {
        $this->sm = $sm;
        $this->metadataManager = $metadataManager;
        $this->lookupDataManager    = $lookupDataManager;
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
        $this->initForm();
    }

    protected function initForm()
    {
        // Load avalilable contents
        $this->availableContents = $this->availableContentsProvider->getAvailableContents($this->entity);

        // Editor tabs
        $containers = array();
        if($this->entity instanceof Document) {
            try {
                $containers = $this->documentApi->getContentContainers($this->entity);
            }
            catch(\Vivo\Repository\Exception\PathNotSetException $e) {

            }
        }
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
        $lookupData = $this->lookupDataManager->getLookupData($metadata, $this->entity);
        $action = $this->request->getUri()->getPath();
        $buttonsFieldset = new Fieldset('buttons');
        $buttonsFieldset->add(array(
            'name' => 'save',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
            ),
        ));

        $form = new EntityEditorForm('entity', $metadata, $lookupData);
        $form->setAttribute('action', $action);
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));
        $form->add($buttonsFieldset);

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

        $success = $success && $this->saveContents();

        if($success) {
            $this->events->trigger(new RedirectEvent());
        }
    }

    protected function saveContents()
    {
        $success = true;

        /* @var $component \Vivo\Backend\UI\Explorer\Editor\ContentTab */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        try {
            $success = $success && $component->save();
        }
        catch(\Exception $e) {
            throw $e;
        }

        if($success) {
            $this->contentTab->removeAllComponents();
            $this->init();

            foreach ($this->contentTab->getComponents() as $component) {
                $component->initForm();
            }
        }

        if($this->alert) {
            if($success) {
                $this->alert->addMessage('Saved...', Alert::TYPE_SUCCESS);
            }
            else {
                $this->alert->addMessage('Error...', Alert::TYPE_ERROR);
            }
        }

        return $success;
    }
}
