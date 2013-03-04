<?php
namespace Vivo\Backend\Provider;

use Vivo\LookupData\AbstractLookupDataProvider;
use Vivo\CMS\Model\Entity;

class EntityResource extends AbstractLookupDataProvider
{
    /**
     * @return array
     */
    public function getLookupData($property, array $propertyMetadata, Entity $entity)
    {
        $return = array('' => '');

        /* @var $cms \Vivo\CMS\Api\CMS */
        $cms = $this->serviceManager->get('Vivo\CMS\Api\CMS');

        foreach ($cms->scanResources($entity) as $name) {
            $return[$name] = $name;
        }

        return $return;
    }

}
