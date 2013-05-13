<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\Alert;
use Vivo\Form\Fieldset;
use Vivo\Util\RedirectEvent;
use Vivo\CMS\Model\Folder;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\LookupData\LookupDataManager;
use Vivo\Metadata\MetadataManager;
use Vivo\CMS\AvailableContentsProvider;

use Zend\ServiceManager\ServiceManager;

/**
 * Creator
 */
class Creator extends Editor
{
    /**
     * @var \Vivo\Backend\UI\Explorer\ExplorerInterface
     */
    private $explorer;

    /**
     * Parent folder for the entity being created
     * @var Folder
     */
    protected $parentFolder;

    /**
     * Indexer API
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Constructor
     * @param ServiceManager $sm
     * @param MetadataManager $metadataManager
     * @param LookupDataManager $lookupDataManager
     * @param DocumentApiInterface $documentApi
     * @param AvailableContentsProvider $availableContentsProvider
     * @param IndexerApiInterface $indexerApi
     */
    public function __construct(ServiceManager $sm,
                                MetadataManager $metadataManager,
                                LookupDataManager $lookupDataManager,
                                DocumentApiInterface $documentApi,
                                AvailableContentsProvider $availableContentsProvider,
                                IndexerApiInterface $indexerApi)
    {
        parent::__construct($sm, $metadataManager, $lookupDataManager, $documentApi, $availableContentsProvider);
        $this->indexerApi   = $indexerApi;
    }

    public function init()
    {
        $this->explorer = $this->getParent('Vivo\Backend\UI\Explorer\ExplorerInterface');
        $this->parentFolder = $this->explorer->getEntity();
        $this->doCreate();

        $this->initForm();
    }

    public function create()
    {
    }

    /**
     * Returns path which should be passed to availableContentsProvider
     * @return string
     */
    protected function getPathForContentsProvider()
    {
        return $this->parentFolder->getPath();
    }

    protected function doGetForm()
    {
        $form = parent::doGetForm();
        $form->add(array(
            'name' => '__type',
            'type' => 'Vivo\Form\Element\Select',
            'attributes' => array(
                'options' => array(
                    'Vivo\CMS\Model\Document' => 'Vivo\CMS\Model\Document',
                    'Vivo\CMS\Model\Folder' => 'Vivo\CMS\Model\Folder',
                )
            )
        ));
        return $form;
    }

    private function doCreate()
    {
        $entityClass = $this->request->getPost('__type', 'Vivo\CMS\Model\Document');
        $this->entity = new $entityClass;
        $this->resetForm();
        $this->getForm()->bind($this->entity);
        $this->getForm()->get('__type')->setValue($entityClass);
        $this->loadFromRequest();
    }

    public function save()
    {
        if($this->getForm()->isValid()) {
            $parent = $this->explorer->getEntity();
            $this->entity = $this->documentApi->createDocument($parent, $this->entity);
            $this->explorer->setEntity($this->entity);
            $this->saveProcess();

            //Reindex the entity again (the entity was indexed as part of createDocument(), but the information on
            //published contents was not indexed correctly as the contents were not saved yet and their status
            //might have been changed
            $this->indexerApi->saveEntity($this->entity);

            $this->explorer->setCurrent('editor');
            $this->events->trigger(new RedirectEvent());
            $this->addAlertMessage('Created...', Alert::TYPE_SUCCESS);
        }
        else {
            $this->addAlertMessage('Error...', Alert::TYPE_ERROR);
        }
    }
}
