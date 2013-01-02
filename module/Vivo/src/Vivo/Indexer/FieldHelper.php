<?php
namespace Vivo\Indexer;

/**
 * FieldHelper
 */
class FieldHelper implements FieldHelperInterface
{
    /**
     * Field definition
     * @var array
     */
    protected $fieldDef         = array();

    /**
     * Array of supported field types
     * @var array
     */
    protected $fieldTypes       = array(
        self::FIELD_TYPE_STRING_I,
        self::FIELD_TYPE_STRING_IM,
        self::FIELD_TYPE_STRING_S,
        self::FIELD_TYPE_STRING_SM,
        self::FIELD_TYPE_STRING_IS,
        self::FIELD_TYPE_STRING_IST,
        self::FIELD_TYPE_STRING_ISM,
    );

    /**
     * Constructor
     * @param array $fieldDef
     */
    public function __construct(array $fieldDef)
    {
        $this->fieldDef = $fieldDef;
    }

    /**
     * Returns type of the submitted field name
     * @param string $fieldName
     * @return string
     */
    public function getFieldType($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fieldDef)) {
            $this->addFieldDefFromEntityMetadata($fieldName);
        }
        return $this->fieldDef[$fieldName];
    }

    /**
     * Returns true when the specified field exists
     * @param string $fieldName
     * @return bool
     */
    public function fieldExists($fieldName)
    {
        if (array_key_exists($fieldName, $this->fieldDef)) {
            return true;
        }
        try {
            $this->addFieldDefFromEntityMetadata($fieldName);
        } catch (Exception\UnknownFieldException $e) {
            return false;
        }
        if (array_key_exists($fieldName, $this->fieldDef)) {
            return true;
        }
        return false;
    }

    /**
     * Looks up field definition in entity metadata and adds it to field definition array
     * @param string $fieldName
     * @throws Exception\UnknownFieldException
     */
    protected function addFieldDefFromEntityMetadata($fieldName)
    {
        //TODO - implement addFieldDefFromEntityMetaData()
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }
}
