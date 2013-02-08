<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\EntityEditor as EntityEditorForm;

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
        $this->entity = $this->getParent()->getEntity();

        $this->getForm()->bind($this->entity);

        /* @var $contentContainer \Vivo\CMS\Model\ContentContainer */
        foreach ($this->cms->getContentContainers($this->entity) as $index => $contentContainer) {
//             echo 'content'.$index."\n";
            $this->contentTab->addComponent($this->getContentForm($contentContainer), "content_$index");
        }

        parent::init();
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

        foreach ($this->getComponent('contentTab')->getComponents() as $name => $component) {
            echo 'save: '.$name."<br>\n";
            $component->save();
        }

        if ($form->isValid()) {
//             print_r($this->entity);

//             $this->redirector->redirect();
        }
    }
}