<?php
namespace Vivo\LookupData;

use Vivo\CMS\Model\Entity;

use Zend\ServiceManager\ServiceManager;

/**
 * LookupDataManager
 */
class LookupDataManager
{
    /**
     * Name of the metadata key containing the LookupDataProvider class name
     * @var string
     */
    protected $metadataKey  = 'lookup';

    /**
     * Service Manager
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager   = $serviceManager;
    }

    /**
     * Returns lookup data for an entity
     * @param array $metadata
     * @param \Vivo\CMS\Model\Entity $entity
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function getLookupData(array $metadata, Entity $entity)
    {
        $lookupData = array();
        foreach ($metadata as $property => $propertyMetadata) {
            $lookupData[$property]  = array();
            if (array_key_exists($this->metadataKey, $propertyMetadata)) {
                $providerClass  = $propertyMetadata[$this->metadataKey];
                if (!class_exists($providerClass)) {
                    throw new Exception\InvalidArgumentException(
                        sprintf("%s: Lookup data provider class '%s' does not exist", __METHOD__, $providerClass));
                }
                if (PHP_VERSION_ID >= 50307) {
                    //PHP version with correct implementation of is_subclass_of
                    if (!is_subclass_of($providerClass, 'Vivo\LookupData\LookupDataProviderInterface')) {
                        throw new Exception\InvalidArgumentException(
                            sprintf("%s: Lookup data provider class '%s' must implement "
                                    . "'Vivo\\LookupData\\LookupDataProviderInterface'", __METHOD__, $providerClass));
                    }
                    $provider   = $this->serviceManager->get($providerClass);
                } else {
                    //Old php version fix 5.3.7
                    $provider   = $this->serviceManager->get($providerClass);
                    if (!($provider instanceof \Vivo\LookupData\LookupDataProviderInterface)) {
                        throw new Exception\InvalidArgumentException(
                            sprintf("%s: Lookup data provider class '%s' must implement "
                                . "'Vivo\\LookupData\\LookupDataProviderInterface'", __METHOD__, $providerClass));

                    }
                }
                $lookupData[$property]  = $provider->getLookupData($property, $propertyMetadata, $entity);
            };
        }
        return $lookupData;
    }
}