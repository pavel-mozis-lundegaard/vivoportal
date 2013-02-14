<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Overview extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\Overview
     */
    private $content;

    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function init()
    {
        parent::init();

        $this->getForm()->bind($this->content);
    }

    public function save()
    {
        //TODO
        $this->getForm()->isValid();
    }

    public function doGetForm()
    {
        $form = new Form('editor');
        $form->setHydrator(new ClassMethodsHydrator(false));
        $form->setOptions(array('use_as_base_fieldset' => true));
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
