<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Form\Fieldset;
use Vivo\Backend\UI\Form\EntityEditor as EntityEditorForm;
use Vivo\CMS\AvailableContentsProvider;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\ContentContainer;

class Creator extends Editor
{
    /**
     * @var \Vivo\Backend\UI\Explorer\Explorer //TODO: ExplorerInterface
     */
    private $explorer;

    public function init()
    {
        $this->explorer = $this->getParent('Vivo\Backend\UI\Explorer\Explorer'); //TODO: ExplorerInterface
        $this->doCreate();

        parent::initForm();
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

    public function create() { }

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

            parent::saveContents();
        }
    }
}
