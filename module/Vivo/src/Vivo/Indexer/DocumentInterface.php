<?php
namespace Vivo\Indexer;

/**
 * DocumentInterface
 */
interface DocumentInterface extends \IteratorAggregate
{
    /**
     * Adds a field to the document
     * @param Field $field
     */
    public function addField(Field $field);

    /**
     * Returns a field from the document
     * @param string $fieldName
     * @return Field
     * @throws Exception\InvalidArgumentException
     */
    public function getField($fieldName);

    /**
     * Returns array of filed names present in the document
     * @return string[]
     */
    public function getFieldNames();

    /**
     * Returns field value
     * @param string $filedName
     * @return string
     */
    public function getFieldValue($filedName);
}
