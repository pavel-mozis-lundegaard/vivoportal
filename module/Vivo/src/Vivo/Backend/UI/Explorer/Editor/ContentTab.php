<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\UI\AbstractForm;
use Vivo\UI\TabContainerItemInterface;
use Vivo\Form\Form;

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
    private $contents = array();
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\CMS\Api\Document $documentApi
     */
    public function __construct(\Zend\ServiceManager\ServiceManager $sm, \Vivo\CMS\Api\Document $documentApi)
    {
        $this->sm = $sm;
        $this->documentApi = $documentApi;
        $this->autoAddCsrf = false;
    }

    /**
     * @param \Vivo\CMS\Model\ContentContainer $contentContainer
     */
    public function setContentContainer(\Vivo\CMS\Model\ContentContainer $contentContainer)
    {
        $this->contentContainer = $contentContainer;
    }

    public function init()
    {
        try {
            $this->contents = $this->documentApi->getContentVersions($this->contentContainer);
        }
        catch(\Exception $e) {
            $this->contents = array();
        }

        parent::init();
        $this->doChangeVersion();
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

        $values = array_keys($options);

        $form = new Form('contentTabEditor');
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

        parent::init();
    }

    /**
     * @return boolean
     */
    public function save()
    {
        // Reload version selecbox
        $result = $this->contentEditor->save();

        if($result) {
            $value = $this->getForm()->get('version')->getValue();
            $this->resetForm();
            $this->getForm()->get('version')->setValue($value);
        }

        return $result;
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

}
