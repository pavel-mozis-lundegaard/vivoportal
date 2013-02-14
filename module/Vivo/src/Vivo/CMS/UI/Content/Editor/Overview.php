<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;

class Overview extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content
     */
    private $content;

    public function __construct()
    {

    }

    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function save()
    {
        $form = $this->getForm();

        if($form->isValid()) {

        }
    }

    public function doGetForm()
    {
        $form = new Form('editor');
        $form->add(array(
            'name' => 'type',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array('label' => 'type'),
        ));
        $form->add(array(
            'name' => 'items',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array('label' => 'items'),
        ));
        $form->add(array(
            'name' => 'path',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array('label' => 'path'),
        ));
        $form->add(array(
            'name' => 'criteria',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array('label' => 'criteria'),
        ));
        $form->add(array(
            'name' => 'limit',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array('label' => 'limit'),
        ));

        return $form;
    }

}
