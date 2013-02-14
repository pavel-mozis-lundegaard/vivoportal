<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\UI\AbstractForm;
use Vivo\Form\Form;

class File extends AbstractForm implements ContentEditorInterface
{
    public function save()
    {
        $form = $this->getForm();

        if($form->isValid()) {
            echo $form->get('resource')->getValue();
        }
    }

    public function doGetForm()
    {
        $form = new Form('editor');
        $form->add(array(
            'name' => 'resource',
            'type' => 'Vivo\Form\Element\Textarea',
            'options' => array(
                'label' => 'resource',
                'rows' => 10,
                'cols' => 5,
            ),
        ));

        return $form;
    }
}
