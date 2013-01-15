<?php
namespace Vivo\Indexer;

use Vivo\CMS\Model\Entity;

/**
 * FieldHelperInterface
 */
interface FieldHelperInterface
{
    /**
     * Returns indexer configuration for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return array
     */
    public function getIndexerConfig(Entity $entity, $property);

    /**
     * Returns if the specified property is enabled for indexing
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return boolean
     */
    public function isEnabled(Entity $entity, $property);

    /**
     * Returns indexer field name for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return string
     */
    public function getName(Entity $entity, $property);

    /**
     * Returns indexer field type for the specified property
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return mixed
     */
    public function getType(Entity $entity, $property);

    /**
     * Returns true when the specified property is indexed
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return bool
     */
    public function isIndexed(Entity $entity, $property);

    /**
     * Returns if the specified property is stored in the index
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return bool
     */
    public function isStored(Entity $entity, $property);

    /**
     * Returns if the specified property is tokenized
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return boolean
     */
    public function isTokenized(Entity $entity, $property);

    /**
     * Returns if the specified property is a multi-value
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return boolean
     */
    public function isMultiValue(Entity $entity, $property);

    /**
     * Returns full property name derived from entity and the bare property name
     * @param \Vivo\CMS\Model\Entity $entity
     * @param string $property
     * @return string
     */
    public function getFullPropertyName(Entity $entity, $property);


}
