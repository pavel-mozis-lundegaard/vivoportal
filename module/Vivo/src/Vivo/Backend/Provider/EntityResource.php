<?php
namespace Vivo\Backend\Provider;

use Vivo\LookupData\LookupDataProviderInterface;
use Vivo\CMS\Model\Entity;

class EntityResource implements LookupDataProviderInterface
{

    public function getLookupData($property, array $propertyMetadata, Entity $entity)
    {
        return array(1 , 2, strtoupper($property) => $property);
    }

}
