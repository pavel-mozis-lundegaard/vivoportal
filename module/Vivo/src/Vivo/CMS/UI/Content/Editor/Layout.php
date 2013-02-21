<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;

class Layout extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\Layout
     */
    private $content;
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    public function __construct(Api\Document $documentApi)
    {
        $this->documentApi = $documentApi;
        $this->autoAddCsrf = false;
    }

    public function setContent(Model\Content $content)
    {
        $this->content = $content;
    }

    public function init()
    {
        $value = '';
        foreach ($this->content->getPanels() as $name=>$path) {
            $value.= sprintf('%s:%s%s', $name, $path, PHP_EOL);
        }
        $value = trim($value); // Remove last PHP_EOL

        $this->getForm()->get('panels')->setValue($value);

        parent::init();
    }

    public function save(Model\ContentContainer $contentContainer)
    {
        $form = $this->getForm();

        if($form->isValid()) {
            $contentPanels = array();
            $panelsValue = trim($form->get('panels')->getValue());

            if($panelsValue) {
                $panels = explode(PHP_EOL, $panelsValue);

                foreach ($panels as $data) {
                    list($name, $path) = explode(':', $data);

                    $contentPanels[$name] = $path;
                }
            }

            $this->content->setPanels($contentPanels);

            if($this->content->getUuid()) {
                $this->documentApi->saveContent($this->content);
            }
            else {
                $this->documentApi->createContent($contentContainer, $this->content);
            }
        }
    }

    public function doGetForm()
    {
        $form = new Form('editor-'.$this->content->getUuid());
        $form->setWrapElements(true);
        $form->setOptions(array('use_as_base_fieldset' => true));
        $form->add(array(
            'name' => 'panels',
            'type' => 'Vivo\Form\Element\Textarea',
            'options' => array(
                'label' => 'panels',
                'rows' => 10,
                'cols' => 5,
            ),
        ));

        return $form;
    }
}
