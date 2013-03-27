<?php
namespace Vivo\Indexer;

/**
 * Field
 * Represents a document field
 */
class Field
{
    /**
     * Field name
     * @var string
     */
    protected $name;

    /**
     * Field value
     * @var mixed|array
     */
    protected $value;

    /**
     * Constructor
     * @param string $name
     * @param mixed|array $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Returns the name of the field
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return value of the field
     * @return mixed|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns if the field is multivalued
     * @return bool
     */
    public function isMultiValued()
    {
        return is_array($this->value);
    }
}
