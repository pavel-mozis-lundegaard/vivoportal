<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\Repository\Exception\PathNotSetException;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class File extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\File
     */
    private $content;
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    public function __construct(\Vivo\CMS\Api\CMS $cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function init()
    {
        parent::init();

        try {
            $data = $this->cmsApi->getResource($this->content, 'resource.html');
        }
        catch (PathNotSetException $e) {
            $data = null;
        }

        $form = $this->getForm();
        $form->bind($this->content);
        $form->get('resource')->setValue($data);
    }

    public function save()
    {
        $form = $this->getForm();

        if($form->isValid()) {
            $data = $form->get('resource')->getValue();

            $this->cmsApi->saveResource($this->content, 'resource.html', $data);
        }
    }

    public function doGetForm()
    {
        $form = new Form('editor');
        $form->setHydrator(new ClassMethodsHydrator(false));
        $form->setOptions(array('use_as_base_fieldset' => true));
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
