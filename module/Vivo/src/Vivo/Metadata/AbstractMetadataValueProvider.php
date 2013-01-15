<?php
namespace Vivo\Metadata;

use Zend\ServiceManager\ServiceManager;

/**
 * AbstractMetadataValueProvider
 */
abstract class AbstractMetadataValueProvider implements MetadataValueProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
    }

}
