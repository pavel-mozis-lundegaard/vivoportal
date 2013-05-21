<?php
namespace Vivo\CMS\UI\Content\Editor;

// use Vivo\CMS\Api;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
// use Vivo\Repository\Exception\PathNotSetException;
// use Vivo\CMS\RefInt\SymRefConvertorInterface;
// use Vivo\IO\InputStreamInterface;
// use Vivo\IO\FileInputStream;
// use Vivo\CMS\Model\ContentContainer;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Fileboard extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\File
     */
    private $content;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * (non-PHPdoc)
     * @see Vivo\CMS\UI\Content\Editor.EditorInterface::setContent()
     */
    public function setContent(Model\Content $content)
    {
        $this->content = $content;
    }

    public function save(Model\ContentContainer $container)
    {

    }

    /**
     * (non-PHPdoc)
     * @see Vivo\UI.AbstractForm::doGetForm()
     */
    public function doGetForm()
    {
        $form = new Form('content-resource-form'.$this->content->getUuid());
        $form->setWrapElements(true);
        $form->setHydrator(new ClassMethodsHydrator(false));
        $form->setOptions(array('use_as_base_fieldset' => true));

        if($this->content->getCreated()) {
            $form->add($this->getEditorFormFields());
        }
        else {
            //$form->add($this->getFirstFormFields());
        }

        return $form;
    }

    private function getFirstFormFields()
    {
        return array();
    }

    private function getEditorFormFields()
    {
        return array(
            'name' => 'upload-file',
            'type' => 'Vivo\Form\Element\File',
            'attributes' => array(
                    'id'   => 'content-resource-upload-'.$this->content->getUuid(),
            ),
            'options' => array(
                    'label' => 'resource',
            ),
        );
    }

}
