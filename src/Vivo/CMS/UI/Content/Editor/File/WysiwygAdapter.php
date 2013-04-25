<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Vivo\CMS\Api;

/**
 * Editor Adapter for editing HTML code via WYSIWYG Editor
 */
class WysiwygAdapter extends AbstractAdapter implements DataEditorInterface
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
	 * Constructs Adapter
	 */
	public function __construct()
	{

	}

	/**
	 * Initializes Adapter
	 */
	public function init()
	{
		parent::init();

		$this->form = $this->getForm();
		$this->form->setAttribute('method', 'post');

		//$this->form->get('wysiwyg')->getValue()
		//$this->form->get("wysiwyg")->setAttribute('value', val);
	}

	/**
	 * Creates form
	 */
	protected function doGetForm()
	{
		$factory = new Factory();
		return $factory->createForm(array(
				'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
				'elements' => array(
					array('spec' => array(
							'type' => 'Vivo\Form\Element\Textarea',
							'name' => 'wysiwyg',
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
		if($this->data === $this->getData()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Vivo\CMS\UI\Content\Editor\File.DataEditorInterface::setData()
	 */
	public function setData($data)
	{
		$this->data = $data;
		$this->form->get("wysiwyg")->setValue($data);
	}

	/**
	 * (non-PHPdoc)
	 * @see Vivo\CMS\UI\Content\Editor\File.DataEditorInterface::getData()
	 */
	public function getData()
	{
		if($this->form->isValid()) {
			$this->form->get("wysiwyg")->getValue();
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
