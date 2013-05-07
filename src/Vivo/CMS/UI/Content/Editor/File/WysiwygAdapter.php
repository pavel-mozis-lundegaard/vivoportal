<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Vivo\CMS\Api;
use Vivo\Form\Factory as FormFactory;
use Vivo\CMS\RefInt\SymRefConvertorInterface;
use Vivo\CMS\UI\Content\Editor\AbstractAdapter;
use Vivo\CMS\UI\Content\Editor\ResourceEditorInterface;
use Vivo\Repository\Exception\PathNotSetException;

/**
 * Editor Adapter for editing HTML code via WYSIWYG Editor
 */
class WysiwygAdapter extends AbstractAdapter implements ResourceEditorInterface
{
	/**
	 * Form textarea for WYSIWYG editor
	 * @var Vivo\UI\Form
	 */
	protected $form;

	/**
	 * Edited html code
	 * @var string
	 */
	protected $data;

	/**
	 * Constructor
	 * @param Api\CMS $cmsApi
	 * @param SymRefConvertorInterface $symRefConvertor
	 * @param FormFactory $formFactory
	 */
	public function __construct(Api\CMS $cmsApi, SymRefConvertorInterface $symRefConvertor, FormFactory $formFactory)
	{
	    $this->cmsApi           = $cmsApi;
	    $this->symRefConvertor  = $symRefConvertor;
	    $this->formFactory      = $formFactory;
	}

    /**
	 * Initializes Adapter
	*/
    public function init()
    {
        parent::init();
        $form = $this->getForm();
        $form->setAttribute('method', 'post');
        try {
            if($this->content->getUuid()) {
                $data = $this->cmsApi->getResource($this->content, 'resource.html');
                $this->data = $data;
                $data = $this->symRefConvertor->convertReferencesToURLs($data);
                $form->get("resource")->setValue($data);
            }
        }
        catch (PathNotSetException $e) {

        }
    }

	/**
	 * Creates form
	 */
	protected function doGetForm()
    {
        return $this->formFactory->createForm(array(
            'name' => 'editor-'.$this->content->getUuid(),
            'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                'elements' => array(
                   array('spec' => array(
                            'type' => 'Vivo\Form\Element\Textarea',
                            'name' => 'resource',
                            'attributes' => array(
                                'rows' => 10,
                                'cols' => 30,
                                'id'   => 'content-resource-'.$this->content->getUuid(),
                            ),
                            'options' => array(
                                'label' => 'Wysiwig',
                            )
                        ),
                    ),
                ),
        ));
    }

	/**
	 * (non-PHPdoc)
	 * @see Vivo\CMS\UI\Content\Editor\File.DataEditorInterface::dataChanged()
	 */
	public function dataChanged()
    {
        return ($this->data != $this->getData());
    }

    /**
	 * (non-PHPdoc)
	 * @see Vivo\CMS\UI\Content\Editor\File.DataEditorInterface::getData()
	 */
    public function getData()
    {
        $this->loadFromRequest();
        $form = $this->getForm();
        if($form->isValid()) {
            $data = $form->get("resource")->getValue();
            $data   = $this->symRefConvertor->convertUrlsToReferences($data);
            return $data;
        }
    }

	/**
	 * View Adapter
	 */
	public function view()
	{
		return parent::view();
	}

}
