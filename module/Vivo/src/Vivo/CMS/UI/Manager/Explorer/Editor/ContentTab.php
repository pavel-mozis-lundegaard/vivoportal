<?php
namespace Vivo\CMS\UI\Manager\Explorer\Editor;

use Vivo\CMS\UI\AbstractForm;
use Vivo\UI\TabContainerItemInterface;

class ContentTab extends AbstractForm implements TabContainerItemInterface
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

    public function initEdior()
    {
        try {
            $this->contents = $this->documentApi->getContentVersions($this->contentContainer);
        }
        catch(\Exception $e) {
            $this->contents = array();
        }

        // Sets 1 entity
        if($this->entity == null && count($this->contents)) {
            $this->entity = $this->contents[0];
        }

        $component = new Content($this->documentApi, $this->metadataManager, $this->contentContainer, $this->entity);
        $component->setRequest($this->request);
        $component->setView(new \Zend\View\Model\ViewModel());

        $this->addComponent($component, 'contentEditor');

        $this->contentEditor->init();
    }

    protected function doGetForm()
    {
        $options = array();
        foreach ($this->contents as $k => $content) { /* @var $content \Vivo\CMS\Model\Content */
            $options['EDIT:'.$content->getUuid()] = sprintf('1.%d [%s] %s {%s}',
                    $k, $content->getState(), get_class($content), $content->getUuid());
        }

        $options['NEW:Vivo\CMS\Model\Content\File'] = sprintf('[%s] %s', 'NEW', 'Vivo\CMS\Model\Content\File');
        $options['NEW:Vivo\CMS\Model\Content\Overview'] = sprintf('[%s] %s', 'NEW', 'Vivo\CMS\Model\Content\Overview');

        $form = new \Vivo\Form\Form('contentTabEditor');
        $form->add(array(
                'name' => 'version',
                'type' => 'Vivo\Form\Element\Select',
                'attributes' => array('options' => $options),
        ));

        return $form;
    }

    public function changeVersion()
    {
        $version = $this->getForm()->get('version')->getValue();

// print_r(explode(':', $version));

        list($type, $param) = explode(':', $version);

        if($type == 'NEW') {
            $this->entity = new $param;
            $this->removeComponent('contentEditor');
            $this->initEdior();
        }
        elseif($type == 'EDIT') {
            foreach ($this->contents as $content) {
                if($content->getUuid() == $param) {
                    $this->entity = $content;
                    break;
                }
            }

            $this->removeComponent('contentEditor');
            $this->initEdior();
        }
    }

    /**
     * @return boolean
     */
    public function save()
    {
        return $this->contentEditor->save();
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

    public function view()
    {
        $this->view->entity = $this->entity;

        return parent::view();
    }

}
