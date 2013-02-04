<?php
namespace Vivo\CMS\UI\Manager\Form\Fieldset;

use Vivo\Form\Fieldset;

/**
 * EntityEditor fieldset.
 */
class EntityEditor extends Fieldset
{
    /**
     * Constructor.
     *
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        parent::__construct('entity-editor');

        foreach ($metadata as $name => $attrs) {
            // Options
            $options = array(
                 //@TODO: human name
                'label' => $name,
            );

            if(!empty($attrs['important'])) {
                $options['class'] = 'important';
            }

            // Attributes
            $attributes = array();

            if(!empty($attrs['options']) && is_array($attrs['options'])) {
                $attributes['options'] = $attrs['options'];
            }
            if(!empty($attrs['field_attributes']) && is_array($attrs['field_attributes'])) {
                $attributes = array_merge($attributes, $attrs['field_attributes']);
            }
            if(!empty($attrs['important'])) {
                $attributes['class'] = 'important';
            }

            // Field init
            $this->add(array(
                'name' => $name,
                'type' => $this->getFieldType($attrs['field_type']),
                'options' => $options,
                'attributes' => $attributes,
            ));
        }
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getFieldType($type)
    {
        if(strpos($type, '\\') && class_exists($type)) {
            return $type;
        }

        $elementClass = 'Vivo\Form\Element\\'.ucfirst($type);

        return $elementClass;
    }

}

