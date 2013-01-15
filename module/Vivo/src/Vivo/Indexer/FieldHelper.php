<?php
namespace Vivo\Indexer;

use Vivo\Metadata\MetadataManager;
use Vivo\Storage\PathBuilder\PathBuilder;
use Vivo\CMS\Model\Entity;
use Vivo\Indexer\IndexerInterface;

/**
 * FieldHelper
 */
class FieldHelper implements FieldHelperInterface
{
    /**
     * Indexer configurations for individual properties
     * array(
     *      'entity_class_name' => array(
     *          'propertyName'  => array(
     *              'enabled'   => true|false,              //Shall this property be included in index?
     *              'name'      => 'field_name_in_index',   //Field name in index
     *              'type'      => 'field_type',            //Indexer field type
     *              'options'   => array(
     *                  'indexed'   => true|false,
     *                  'stored'    => true|false,
     *                  'tokenized' => true|false,
     *                  'multi'     => true|false,          //Is this property multi-valued?
     *              ),
     *          ),
     *      ),
     * )
     * @var array
     */
    protected $indexerConfigs         = array();

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
     * Default options used when metadata does not specify them
     * The property metadata is merged with these defaults
     * @var array
     */
    protected $defaultIndexingOptions   = array(
        'indexed'       => false,
        'type'          => IndexerInterface::FIELD_TYPE_STRING,
        'options'       => array(
            'indexed'       => true,
            'stored'        => true,
            'tokenized'     => false,
            'multi_value'   => false,
        ),
    );

    /**
     * Array of supported field types
     * @var array
     */
    protected $fieldTypes       = array(
        IndexerInterface::FIELD_TYPE_STRING,
        IndexerInterface::FIELD_TYPE_DATETIME,
        IndexerInterface::FIELD_TYPE_FLOAT,
        IndexerInterface::FIELD_TYPE_INT,
    );

    /**
     * Constructor
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\Storage\PathBuilder\PathBuilder $pathBuilder
     */
    public function __construct(MetadataManager $metadataManager, PathBuilder $pathBuilder)
    {
        $this->metadataManager  = $metadataManager;
        $this->pathBuilder      = $pathBuilder;
    }

    /**
     * Returns indexer configuration for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    public function getIndexerConfig(Entity $entity, $property)
    {
        $entityClass    = get_class($entity);
        $this->loadIndexerConfigs($entity);
        if (!isset($this->indexerConfigs[$entityClass][$property])) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Property '%s' not defined for entity '%s'", __METHOD__, $property, $entityClass));
        }
        return $this->indexerConfigs[$entityClass][$property];
    }

    /**
     * Returns if the specified property is enabled for indexing
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return boolean
     */
    public function isEnabled(Entity $entity, $property)
    {
        $config = $this->getIndexerConfig($entity, $property);
        return $config['enabled'];
    }

    /**
     * Returns indexer field name for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return string
     */
    public function getName(Entity $entity, $property)
    {
        $config = $this->getIndexerConfig($entity, $property);
        return $config['name'];
    }

    /**
     * Returns indexer field type for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return mixed
     */
    public function getType(Entity $entity, $property)
    {
        $config = $this->getIndexerConfig($entity, $property);
        return $config['type'];
    }

    /**
     * Returns true when the specified property is indexed
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return bool
     */
    public function isIndexed(Entity $entity, $property)
    {
        $config = $this->getIndexerConfig($entity, $property);
        return $config['options']['indexed'];
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
     * Looks up indexer configurations in entity metadata and adds it to property definition array
     * @param \Vivo\CMS\Model\Entity $entity
     * @return void
     */
    protected function loadIndexerConfigs(Entity $entity)
    {
        $entityClass    = get_class($entity);
        if (!array_key_exists($entityClass, $this->indexerConfigs)) {
            $entityMetadata = $this->metadataManager->getMetadata($entity);
            foreach ($entityMetadata as $propertyName => $metadata) {
                $fullPropName           = $this->getFullPropertyName($entity, $propertyName);
                $indexerConfig          = $this->defaultIndexingOptions;
                $indexerConfig['name']  = $fullPropName;
                if (isset($metadata['index'])) {
                    $indexerConfig  = array_merge($indexerConfig, $metadata['index']);
                }
                $this->indexerConfigs[$entityClass][$propertyName] = $indexerConfig;
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
//    protected function getIndexerFieldType($type, array $options)
//    {
//        $type               = strtolower($type);
//        switch ($type) {
//            case 'string':
//                $indexerFieldType   = 's-';
//                break;
//            case 'datetime':
//                //TODO - implement datetime support
//                throw new Exception\InvalidArgumentException(
//                    sprintf("%s: Vivo field type 'DateTime' will be supported soon.", __METHOD__));
//                break;
//            default:
//                throw new Exception\InvalidArgumentException(
//                    sprintf("%s: Vivo field type '%s' not supported by Indexer implementation.", __METHOD__, $type));
//                break;
//        }
//        //Indexed
//        if ($options['indexed']) {
//            $indexerFieldType   .= 'i';
//        }
//        //Stored
//        if ($options['stored']) {
//            $indexerFieldType   .= 's';
//        }
//        //Tokenized
//        if ($options['tokenized']) {
//            $indexerFieldType   .= 't';
//        }
//        //Multi-value
//        if ($options['multi_value']) {
//            $indexerFieldType   .= 'm';
//        }
//        if (!in_array($indexerFieldType, $this->fieldTypes)) {
//            throw new Exception\InvalidArgumentException(
//                sprintf("%s: Indexer field type '%s' not supported.", __METHOD__, $indexerFieldType));
//        }
//        return $indexerFieldType;
//    }
}
