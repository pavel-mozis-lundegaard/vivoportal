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
     * @param string $name Form and fieldset name.
     * @param array $metadata
     */
    public function __construct($name, array $metadata)
    {
        parent::__construct($name);

        $this->setAttribute('method', 'post');

        // Fieldset
        $fieldset = new EntityEditorFieldset($name, $metadata);
        $fieldset->setHydrator(new ClassMethodsHydrator(false));
        $fieldset->setOptions(array('use_as_base_fieldset' => true));

        $this->add($fieldset);
    }

}
