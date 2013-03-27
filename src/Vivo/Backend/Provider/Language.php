<?php
namespace Vivo\Backend\Provider;

use Vivo\LookupData\AbstractLookupDataProvider;
use Vivo\CMS\Model\Entity;

class Language extends AbstractLookupDataProvider
{
    /**
     * @return array
     */
    public function getLookupData($property, array $propertyMetadata, Entity $entity)
    {
        $return = array();

        /* @var $doc \Vivo\CMS\Api\DocumentInterface */
        $doc = $this->serviceManager->get('Vivo\CMS\Api\Document');

        foreach ($doc->getAvailableLanguages() as $lang => $name) {
            $return[$lang] = $name;
        }

        return $return;
    }

}
