<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\UI\Content\Editor\ResourceEditorInterface;

use Vivo\CMS\Api;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\Repository\Exception\PathNotSetException;
use Vivo\CMS\RefInt\SymRefConvertorInterface;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class File extends AbstractForm implements EditorInterface, AdapterAwareInterface
{
    const ADAPTER_COMPONENT_NAME    = 'resourceAdapter';

    /**
     * @var \Vivo\CMS\Model\Content\File
     */
    private $content;
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;
    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * Symbolic reference convertor
     * @var SymRefConvertorInterface
     */
    protected $symRefConvertor;

    /**
     * Constructor
     * @param Api\CMS $cmsApi
     * @param Api\Document $documentApi
     * @param SymRefConvertorInterface $symRefConvertor
     */
    public function __construct(Api\CMS $cmsApi, Api\Document $documentApi, SymRefConvertorInterface $symRefConvertor)
    {
        $this->cmsApi           = $cmsApi;
        $this->documentApi      = $documentApi;
        $this->symRefConvertor  = $symRefConvertor;
        $this->autoAddCsrf      = false;
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
    * Sets the editor adapter
    * @param AdapterInterface $adapter
    * @return void
    */
    public function setAdapter(AdapterInterface $adapter = null)
    {
        if($adapter) {
            $this->addComponent($adapter, self::ADAPTER_COMPONENT_NAME);
        }
    }

    /**
     * Returns instance of resource adapter
     * @return child of AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->getComponent(self::ADAPTER_COMPONENT_NAME);
    }

    public function init()
    {
        parent::init();

        $adapter = $this->getComponent(self::ADAPTER_COMPONENT_NAME);
        if ($adapter) {
            $adapter->init();
        }
    }
    /**
     * (non-PHPdoc)
     * @see Vivo\CMS\UI\Content\Editor.EditorInterface::save()
     */
    public function save(Model\ContentContainer $contentContainer)
    {
        // TODO insert checkbox to form (really replace content with file?)
        $replaceContent = true;
        $form = $this->getForm();

        if($form->isValid()) {
            $data = $form->get('upload-file')->getValue();
            $adapter = $this->getComponent(self::ADAPTER_COMPONENT_NAME);

            if ($replaceContent && $data["tmp_name"] != "") {
                $mimeType = $data["type"];
                $extenstion = \Vivo\Util\MIME::getExt($mimeType);
                $this->saveContent($contentContainer, $mimeType, 'resource.'.$extenstion);
                $rawData = file_get_contents($data["tmp_name"]);
                $this->saveResource($rawData);
            } elseif ($adapter instanceof ResourceEditorInterface && $adapter->dataChanged()) {
                $mimeType = $this->content->getMimeType();

                if (!$this->content->getFilename()) {
                    $extenstion = \Vivo\Util\MIME::getExt($mimeType);
                    $this->content->setFilename("resource.".$extenstion);
                }

                $fileName = $this->content->getFilename();
                $this->saveContent($contentContainer, $mimeType, $fileName);
                $this->saveResource($adapter->getData());
            }
        }
    }

    /**
     * Saves content
     * @param ContentContainer $contentContainer
     * @param string $mimeType
     * @param string $extenstion
     */
    public function saveContent($contentContainer, $mimeType, $fileName)
    {
        $this->content->setMimeType($mimeType);
        $this->content->setFilename($fileName);

        if($this->content->getUuid()) {
            $this->documentApi->saveContent($this->content);
        }
        else {
            $this->documentApi->createContent($contentContainer, $this->content);
        }
    }

    /**
     * Saves resource file
     * @param string $data
     */
    public function saveResource ($data) {
        $this->removeAllResources();
        $this->cmsApi->saveResource($this->content, $this->content->getFilename(), $data);
    }

    /**
     * Remove all resources
     */
    public function removeAllResources()
    {
        $resources = $this->cmsApi->scanResources($this->content);
        foreach ($resources as $resource) {
            $this->cmsApi->removeResource($this->content, $resource);
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
        $form->add(array(
                    'name' => 'upload-file',
                    'type' => 'Vivo\Form\Element\File',
                    'attributes' => array(
                        'id'   => 'content-resource-upload-'.$this->content->getUuid(),
                    ),
                    'options' => array(
                        'label' => 'resource',
                    ),
        ));
        return $form;
    }

    /**
     * Returns key under which an editor adapter is searched in configuration
     * @return string
     */
    public function getAdapterKey()
    {
        return $this->content->getMimeType();
    }
}