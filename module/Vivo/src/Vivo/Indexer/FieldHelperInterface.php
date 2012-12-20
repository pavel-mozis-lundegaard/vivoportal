<?php
namespace Vivo\Indexer;

/**
 * FieldHelperInterface
 */
interface FieldHelperInterface
{
    /**
     * Field Type: String - indexed
     */
    const FIELD_TYPE_STRING_I   = 's-i';

    /**
     * Field Type: String - indexed, multi-valued
     */
    const FIELD_TYPE_STRING_IM  = 's-im';

    /**
     * Field Type: String - stored
     */
    const FIELD_TYPE_STRING_S   = 's-s';

    /**
     * Field Type: String - stored, multi-valued
     */
    const FIELD_TYPE_STRING_SM  = 's-sm';

    /**
     * Field Type: String - indexed, stored
     */
    const FIELD_TYPE_STRING_IS  = 's-is';

    /**
     * Field Type: String - indexed, stored, tokenized
     */
    const FIELD_TYPE_STRING_IST = 's-ist';

    /**
     * Field Type: String - indexed, stored, multi-valued
     */
    const FIELD_TYPE_STRING_ISM = 's-ism';

    /**
     * Returns type of the submitted field
     * @param string $fieldName
     * @return string
     */
    public function getFieldType($fieldName);

    /**
     * Returns true when the specified field exists
     * @param string $fieldName
     * @return bool
     */
    public function fieldExists($fieldName);
}
