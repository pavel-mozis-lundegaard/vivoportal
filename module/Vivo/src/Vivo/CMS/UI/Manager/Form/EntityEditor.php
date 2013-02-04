<?php
namespace Vivo\CMS\UI\Manager\Form;

use Vivo\CMS\UI\Manager\Form\Fieldset\EntityEditor as EntityEditorFieldset;
use Vivo\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * EntityEditor form.
 */
class EntityEditor extends Form
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param array $metadata
     */
    public function __construct($name, array $metadata)
    {
        parent::__construct($name);

        $this->setAttribute('method', 'post');

        // Fieldset
        $fieldset = new EntityEditorFieldset($metadata);
        $fieldset->setHydrator(new ClassMethodsHydrator(false));
        $fieldset->setOptions(array('use_as_base_fieldset' => true));

        $this->add($fieldset);

        // Submit
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
            ),
        ));
    }

}
