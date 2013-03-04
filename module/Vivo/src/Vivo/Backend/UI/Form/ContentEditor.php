<?php
namespace Vivo\Backend\UI\Form;

use Vivo\Backend\UI\Form\Fieldset\EntityEditor as EntityEditorFieldset;
use Vivo\Form\Fieldset;
use Vivo\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * ContentEditor form.
 */
class ContentEditor extends Form
{
    /**
     * Constructor.
     *
     * @param string $name Form and fieldset name.
     * @param array $metadata
     */
    public function __construct($name, array $metadata = array())
    {
        parent::__construct($name);

        $this->setWrapElements(true);
        $this->setAttribute('method', 'post');

        // Fieldset
        $fieldset = new EntityEditorFieldset('content', $metadata, array());
        $fieldset->setHydrator(new ClassMethodsHydrator(false));
        $fieldset->setOptions(array('use_as_base_fieldset' => true));
        $fieldset->add(array(
            'name' => 'state',
            'type' => 'Vivo\Form\Element\Radio',
            'attributes' => array(
                'options' => array(
                    'NEW' => 'NEW',
                    'PUBLISHED' => 'PUBLISHED',
                    'ARCHIVED' => 'ARCHIVED'
                )
            )
        ));

        $this->add($fieldset);
    }

}
