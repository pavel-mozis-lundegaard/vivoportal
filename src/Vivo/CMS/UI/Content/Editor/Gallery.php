<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Api\Content\Gallery as GalleryApi;
use Vivo\CMS\Model;
use Vivo\CMS\Model\Content\Fileboard\Separator;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\Form\Fieldset;
use Vivo\Util\RedirectEvent;

class Gallery extends AbstractForm implements EditorInterface
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
     * @var \Vivo\CMS\Api\Content\Gallery
     */
    private $galleryApi;

    /**
     * @var array
     */
    private $files = array();

    /**
     * Constructor
     *
     * @param \Vivo\CMS\Api\Document $documentApi
     * @param \Vivo\CMS\Api\Content\Gallery $galleryApi
     */
    public function __construct(Api\Document $documentApi, GalleryApi $galleryApi)
    {
        $this->autoAddCsrf = false; //FIXME: remove after fieldsets
        $this->documentApi = $documentApi;
        $this->galleryApi = $galleryApi;
    }

    public function init()
    {
        try {
            $this->files = $this->galleryApi->getList($this->content);
        }
        catch (Api\Exception\InvalidPathException $e) {
            $this->files = array();
        }

        parent::init();
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

        }
    }

    /**
     * Delete action.
     *
     * @param string $uuid
     */
    public function delete($uuid)
    {

    }

    /**
     * Deletes all fileboard files.
     */
    public function deleteAll()
    {

    }

    /**
     * Move up action.
     *
     * @param string $uuid Entity UUID.
     */
    public function moveUp($uuid)
    {

    }

    /**
     * Move down action.
     *
     * @param string $uuid Entity UUID.
     */
    public function moveDown($uuid)
    {

    }

    /**
     * Returns the maximum value of the order.
     *
     * @return int
     */
    private function getMaxOrderBy() {

    }

    /**
     * @param string $uuid Entity UUID.
     * @return int
     */
    private function getFileKeyById($uuid) {

    }

    /**
     * (non-PHPdoc)
     * @see Vivo\UI.AbstractForm::doGetForm()
     */
    public function doGetForm()
    {
        $form = new Form('gallery-editor-'.$this->content->getUuid());
        $form->setWrapElements(true);

        return $form;
    }

    public function view()
    {
        $view = parent::view();
        $view->files = $this->files;

        return $view;
    }

}
