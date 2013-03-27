<?php
namespace Vivo\LookupData;

use Zend\ServiceManager\ServiceManager;

/**
 * AbstractLookupDataProvider
 */
abstract class AbstractLookupDataProvider implements LookupDataProviderInterface
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
