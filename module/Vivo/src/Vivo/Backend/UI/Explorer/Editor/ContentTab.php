<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\UI\AbstractForm;
use Vivo\UI\TabContainerItemInterface;
use Vivo\Form\Form;
use Vivo\CMS\Api;
use Vivo\CMS\Model;

class ContentTab extends AbstractForm implements TabContainerItemInterface
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
    private $availableContents = array();

    /**
     * @var array
     */
    private $contents = array();

    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * @var \Vivo\CMS\AvailableContentsProvider
     */
    private $contentsProvider;

    /**
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\CMS\Api\Document $documentApi
     */
    public function __construct(\Zend\ServiceManager\ServiceManager $sm, Api\Document $documentApi)
    {
        $this->sm = $sm;
        $this->documentApi = $documentApi;
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
     * @param array $contents
     */
    public function setAvailableContents(array $contents)
    {
        $this->availableContents = $contents;
    }

    public function init()
    {
        $this->loadContents();
        parent::init();
        $this->doChangeVersion();
    }

    public function initForm()
    {
        $this->loadContents();
        $this->doChangeVersion();
    }

    private function loadContents()
    {
        try {
            $this->contents = $this->documentApi->getContentVersions($this->contentContainer);
        }
        catch(\Vivo\Repository\Exception\ExceptionInterface $e) {
            $this->contents = array();
        }
    }

    protected function doGetForm()
    {
        $options = array();
        foreach ($this->contents as $k => $content) { /* @var $content \Vivo\CMS\Model\Content */
            $options['EDIT:'.$content->getUuid()] = sprintf('1.%d [%s] %s {%s}',
                $k, $content->getState(), get_class($content), $content->getUuid());
        }

        foreach ($this->availableContents as $ac) {
            $options['NEW:'.$ac] = $ac;
        }

        $values = array_keys($options);

        $form = new Form('container-'.$this->contentContainer->getUuid());
        $form->setWrapElements(true);
        $form->add(array(
                'name' => 'version',
                'type' => 'Vivo\Form\Element\Select',
                'attributes' => array('options' => $options, 'value' => $values[0]),
        ));

        return $form;
    }

    public function changeVersion() { }

    private function doChangeVersion()
    {
        /* @var $content \Vivo\CMS\Model\Content */
        $content = null;

        $version = $this->getForm()->get('version')->getValue();

        list($type, $param) = explode(':', $version);

        if($type == 'NEW') {
            $content = new $param;
        }
        elseif($type == 'EDIT') {
            foreach ($this->contents as $c) {
                if($c->getUuid() == $param) {
                    $content = $c;
                    break;
                }
            }
        }

        /* @var $component \Vivo\Backend\UI\Explorer\Editor\Content */
        $component = $this->sm->create('Vivo\Backend\UI\Explorer\Editor\Content');
        $component->setContentContainer($this->contentContainer);
        $component->setContent($content);

        $this->addComponent($component, 'contentEditor');

        $component->init();
    }

    /**
     * @return boolean
     */
    public function save()
    {
        $result = $this->contentEditor->save();

        if($result) {
            // Reload version selectbox
            $value = $this->getForm()->get('version')->getValue();
            $this->resetForm();
            $this->loadContents();
            $this->getForm()->get('version')->setValue($value);
        }

        return $result;
    }

    public function select()
    {

    }

    public function isDisabled()
    {
        return false;
    }

    public function getLabel()
    {
        return $this->contentContainer->getContainerName() ? $this->contentContainer->getContainerName() : '+';
    }

}
