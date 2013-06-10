<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Api\Content\Fileboard as FileboardApi;
use Vivo\CMS\Model;
use Vivo\CMS\Model\Content\Fileboard\Separator;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;

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

            if($this->content->getCreated()) {
                // Upload new file
                if($form->get('fb-new-file')) {
                    $file = $form->get('fb-new-file')->getValue();
                    $name = $form->get('fb-new-name')->getValue();
                    $desc = $form->get('fb-new-desc')->getValue();

                    if($file['error'] != UPLOAD_ERR_NO_FILE && $file['error'] != UPLOAD_ERR_OK) {
                        throw new \Exception(sprintf('%s: File upload error %s', __METHOD__, $file['error']));
                    }
                    if($file['error'] == UPLOAD_ERR_OK) {
                        $this->fileboardApi->createMediaWithUploadedFile($this->content, $file, trim($name), trim($desc));
                    }
                }

                if($form->get('fb-new-separator')) {
                    $html = $form->get('fb-new-separator')->getValue();

                    $this->fileboardApi->createSeparator($this->content, $html);
                }

                // Update current files
                foreach ($this->request->getPost('fb-media') as $uuid=>$data) {
                    $media = $this->fileboardApi->getMedia($uuid);
                    $media->setName(trim($data['name']));
                    $media->setDescription(trim($data['description']));

                    $this->fileboardApi->saveMedia($media);
                }

                foreach ($this->request->getPost('fb-separator') as $uuid=>$data) {
                    $separator = $this->fileboardApi->getMedia($uuid);

                    $this->fileboardApi->saveSeparator($separator, $data['html']);
                }
            }
        }
    }

    /**
     * @param string $uuid
     */
    public function delete($uuid)
    {
        $media = $this->fileboardApi->getMedia($uuid);

        $this->fileboardApi->removeMedia($media);
    }

    /**
     * (non-PHPdoc)
     * @see Vivo\UI.AbstractForm::doGetForm()
     */
    public function doGetForm()
    {
        $form = new Form('fileboard-editor-'.$this->content->getUuid());
//         $form->setWrapElements(true); //FIXME

        if($this->content->getCreated()) {
            $this->getEditorFormFields($form);
        }

        return $form;
    }

    private function getEditorFormFields($form)
    {
        $form->add(array(
            'name' => 'fb-new-file',
            'type' => 'Vivo\Form\Element\File',
            'options' => array(
                'label' => 'new media',
            ),
        ));
        $form->add(array(
            'name' => 'fb-new-name',
            'type' => 'Vivo\Form\Element\Text',
            'options' => array(
                'label' => 'new media name',
            ),
        ));
        $form->add(array(
            'name' => 'fb-new-desc',
            'type' => 'Vivo\Form\Element\Textarea',
            'options' => array(
                'label' => 'new media description',
            ),
        ));
        $form->add(array(
            'name' => 'fb-new-separator',
            'type' => 'Vivo\Form\Element\Textarea',
            'options' => array(
                'label' => 'new media description',
            ),
        ));
    }

    public function view()
    {
        try {
            $files = $this->fileboardApi->getList($this->content);
        }
        catch (Api\Exception\InvalidPathException $e) {
            $files = array();
        }

        $separators = array();
        foreach ($files as $file) {
            if($file instanceof Separator) {
                $separators[$file->getUuid()] = $this->fileboardApi->getResource($file);
            }
        }

        $view = parent::view();
        $view->files = $files;
        $view->separators = $separators;

        return $view;
    }

}
