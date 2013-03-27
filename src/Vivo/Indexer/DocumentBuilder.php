<?php
namespace Vivo\Indexer;

use Vivo\Indexer\Document;
use Vivo\Indexer\Field;

/**
 * DocumentBuilder
 * Facilitates creation of indexer documents
 */
class DocumentBuilder
{
    /**
     * Field names and values
     * @var array
     */
    protected $fields   = array();

    /**
     * Adds a new field to the document
     * @param string $fieldName
     * @param mixed $value
     * @return DocumentBuilder
     */
    public function add($fieldName, $value)
    {
        $this->fields[$fieldName] = $value;
        return $this;
    }

    /**
     * Clears defined fields
     */
    public function clear()
    {
        $this->fields   = array();
    }

    /**
     * Builds indexer document from the defined fields
     * Clears the field definitions
     * @return Document
     */
    public function build()
    {
        $fields = array();
        foreach ($this->fields as $fieldName => $value)
        {
            $fields[]   = new Field($fieldName, $value);
        }
        $doc    = new Document(null, $fields);
        $this->clear();
        return $doc;
    }
}