<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\Repository\Exception\PathNotSetException;
use Vivo\CMS\RefInt\SymRefConvertorInterface;

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

    public function setContent(Model\Content $content)
    {
        $this->content = $content;
    }

    public function init()
    {
        try {
            $data   = $this->cmsApi->getResource($this->content, 'resource.html');
            $data   = $this->symRefConvertor->convertReferencesToURLs($data);
            $this->getForm()->get('resource')->setValue($data);
        }
        catch (PathNotSetException $e) {

        }

        parent::init();
    }

    public function save(Model\ContentContainer $contentContainer)
    {
        $form = $this->getForm();

        if($form->isValid()) {

            if (!$this->content->getMimeType()) {
                $this->content->setMimeType('text/html');
            }
            if (!$this->content->getFilename()) {
                $this->content->setFilename('resource.html');
            }

            if($this->content->getUuid()) {
                $this->documentApi->saveContent($this->content);
            }
            else {
                $this->documentApi->createContent($contentContainer, $this->content);
            }

            $data   = $form->get('resource')->getValue();
            $data   = $this->symRefConvertor->convertUrlsToReferences($data);
            $this->cmsApi->saveResource($this->content, $this->content->getFilename(), $data);
        }
    }

    public function doGetForm()
    {
        $form = new Form('editor-'.$this->content->getUuid());
        $form->setWrapElements(true);
        $form->setHydrator(new ClassMethodsHydrator(false));
        $form->setOptions(array('use_as_base_fieldset' => true));
        $form->add(array(
            'name' => 'resource',
            'type' => 'Vivo\Form\Element\Textarea',
            'attributes' => array(
                'rows' => 10,
                'cols' => 30,
                'id'   => 'content-resource-'.$this->content->getUuid(),
            ),
            'options' => array(
                'label' => 'resource',
            ),
        ));

        return $form;
    }
}
