<?php
namespace Vivo\Indexer;

use Vivo\Metadata\MetadataManager;
use Vivo\CMS\Api\CMS;
use Vivo\Storage\PathBuilder\PathBuilder;
use Vivo\CMS\Model\Entity;

/**
 * FieldHelper
 */
class FieldHelper implements FieldHelperInterface
{
    /**
     * Property definitions
     * @var array
     */
    protected $propertyDefs         = array();

    /**
     * Metadata manager
     * @var MetadataManager
     */
    protected $metadataManager;

    /**
     * Path builder
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * Default indexing options used when metadata does not specify them
     * @var array
     */
    protected $defaultIndexingOptions   = array(
        'indexed'       => true,
        'stored'        => true,
        'tokenized'     => false,
        'multi_value'   => false,
    );

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
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\Storage\PathBuilder\PathBuilder $pathBuilder
     */
    public function __construct(array $fieldDef, MetadataManager $metadataManager, PathBuilder $pathBuilder)
    {
        $this->propertyDefs         = $fieldDef;
        $this->metadataManager  = $metadataManager;
        $this->pathBuilder      = $pathBuilder;
    }

    /**
     * Returns indexer type for the submitted property name
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return string
     */
    public function getIndexerTypeForProperty(Entity $entity, $property)
    {
        $fullPropName   = $this->getFullPropertyName($entity, $property);
        if (!array_key_exists($fullPropName, $this->propertyDefs)) {
            $this->addFieldDefFromEntityMetadata($entity);
        }
        return $this->propertyDefs[$fullPropName];
    }

    /**
     * Returns true when the specified field exists
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return bool
     */
    public function propertyDefinitionExists(Entity $entity, $property)
    {
        $fullPropName   = $this->getFullPropertyName($entity, $property);
        if (array_key_exists($fullPropName, $this->propertyDefs)) {
            return true;
        }
        try {
            $this->addFieldDefFromEntityMetadata($entity);
        } catch (Exception\UnknownFieldException $e) {
            return false;
        }
        if (array_key_exists($fullPropName, $this->propertyDefs)) {
            return true;
        }
        return false;
    }

    /**
     * Returns full property name derived from entity and the bare property name
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return string
     */
    public function getFullPropertyName(Entity $entity, $property)
    {
        $fullPropName   = get_class($entity) . '\\' . $property;
        return $fullPropName;
    }

    /**
     * Looks up property definitions in entity metadata and adds it to property definition array
     * @param \Vivo\CMS\Model\Entity $entity
     * @return void
     */
    protected function addFieldDefFromEntityMetadata(Entity $entity)
    {
        $entityMetadata = $this->metadataManager->getMetadata($entity);
        foreach ($entityMetadata as $propertyName => $metadata) {
            if (isset($metadata['index']['indexed']) && $metadata['index']['indexed']) {
                //Get field type
                if (isset($metadata['type'])) {
                    $type   = $metadata['type'];
                } else {
                    $type   = 'string';
                }
                //Get indexing options
                $options    = $this->defaultIndexingOptions;
                if (isset($metadata['index']['options'])) {
                    $options    = array_merge($options, $metadata['index']['options']);
                }
                $indexerFieldType   = $this->getIndexerFieldType($type, $options);
                $fullPropName       = $this->getFullPropertyName($entity, $propertyName);
                $this->propertyDefs[$fullPropName] = $indexerFieldType;
            }
        }
    }

    /**
     * Returns indexer field type based on the Vivo type and indexing options
     * @param string $type
     * @param array $options
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function getIndexerFieldType($type, array $options)
    {
        $type               = strtolower($type);
        $indexerFieldType   = '';
        switch ($type) {
            case 'string':
                $indexerFieldType   = 's-';
                break;
            case 'datetime':
                //TODO - implement datetime support
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Vivo field type 'DateTime' will be supported soon.", __METHOD__));
                break;
            default:
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Vivo field type '%s' not supported by Indexer implementation.", __METHOD__, $type));
                break;
        }
        //Indexed
        if ($options['indexed']) {
            $indexerFieldType   .= 'i';
        }
        //Stored
        if ($options['stored']) {
            $indexerFieldType   .= 's';
        }
        //Tokenized
        if ($options['tokenized']) {
            $indexerFieldType   .= 't';
        }
        //Multi-value
        if ($options['multi_value']) {
            $indexerFieldType   .= 'm';
        }
        if (!in_array($indexerFieldType, $this->fieldTypes)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Indexer field type '%s' not supported.", __METHOD__, $indexerFieldType));
        }
        return $indexerFieldType;
    }
}
