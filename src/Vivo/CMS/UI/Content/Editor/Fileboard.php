<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Api\Content\Fileboard as FileboardApi;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Fileboard extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\File
     */
    private $content;

    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * @var \Vivo\CMS\Api\Content\Fileboard
     */
    private $fileboardApi;

    /**
     * Constructor
     *
     * @param \Vivo\CMS\Api\Document $documentApi
     * @param \Vivo\CMS\Api\Content\Fileboard $fileboardApi
     */
    public function __construct(Api\Document $documentApi, FileboardApi $fileboardApi)
    {
        $this->autoAddCsrf = false; //FIXME: remove after fieldsets
        $this->documentApi = $documentApi;
        $this->fileboardApi = $fileboardApi;
    }

    /**
     * (non-PHPdoc)
     * @see Vivo\CMS\UI\Content\Editor.EditorInterface::setContent()
     */
    public function setContent(Model\Content $content)
    {
        $this->content = $content;
    }

    /**
     * @see \Vivo\CMS\UI\Content\Editor\EditorInterface::save()
     */
    public function save(Model\ContentContainer $container)
    {
        $form = $this->getForm();

        if($form->isValid()) {
            if($this->content->getUuid()) {
                $this->documentApi->saveContent($this->content);
            }
            else {
                $this->documentApi->createContent($container, $this->content);
            }

            $file = $form->get('upload-file')->getValue();
            print_r($file);

            if($file['error'] != 0) {
                throw new \Exception(sprintf('%s: File upload error %s', __METHOD__, $file['error']));
            }

            $media = new Model\Content\Fileboard\Media();
            $media->setFilename($file['name']);
            $media->setMimeType($file['type']);

            $this->fileboardApi->addMedia($media);
        }
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
            $this->getEditorFormFields($form);
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

    private function getEditorFormFields($form)
    {
        $form->add(array(
            'name' => 'upload-file',
            'type' => 'Vivo\Form\Element\File',
            /*'attributes' => array(
                'id' => 'content-resource-upload-'.$this->content->getUuid(),
            ),*/
            'options' => array(
                'label' => 'new media',
            ),
        ));
    }

}
