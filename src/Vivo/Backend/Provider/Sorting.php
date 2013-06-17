<?php
namespace Vivo\Backend\Provider;

use Vivo\LookupData\AbstractLookupDataProvider;
use Vivo\CMS\Model\Entity;

/**
 * Provider of sorting options used in Document/Folder
 */
class Sorting extends AbstractLookupDataProvider
{
    /**
     * Provides sorting options from config file used by Document/Folder
     * 
     * @param string $property
     * @param array $propertyMetadata
     * @param Vivo\CMS\Model\Entity $entity
     * @return array
     */
    public function getLookupData($property, array $propertyMetadata, Entity $entity)
    {
        $return = array('' => '');
        $config = $this->serviceManager->get('cms_config');
        
        if(isset($config['document_sorting'])){
            $return = $config['document_sorting']['native'];     
            //unsetting parent option, because its intended to overview and navigation, not for document
            unset($return['parent']);
        }
        return $return;
    }

}
