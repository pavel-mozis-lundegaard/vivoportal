<?php
namespace Vivo\LookupData;

use Vivo\CMS\Model\Entity;

/**
 * LookupDataProviderInterface
 */
interface LookupDataProviderInterface
{
    /**
     * Returns data to be used as lookup data for an entity property
     * @param string $property
     * @param array $propertyMetadata
     * @param \Vivo\CMS\Model\Entity $entity
     * @return array
     */
    public function getLookupData($property, array $propertyMetadata, Entity $entity);
}
