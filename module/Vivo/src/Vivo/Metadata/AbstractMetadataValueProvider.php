<?php
namespace Vivo\Metadata;

use Zend\ServiceManager\ServiceManager;

abstract class AbstractMetadataValueProvider implements MetadataValueProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
    }

}
