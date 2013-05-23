<?php
namespace Vivo\Cache;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;

/**
 * CacheAbstractFactory
 * Service manager abstract factory to create cache instances
 */
class CacheAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Abstract factory options
     * array (
     *      'cache_name'    => array(
     *          //Options to pass to StorageFactory::factory(), e.g.:
     *          'adapter'   => 'apc',
     *          'plugins'   => array(
     *              'exception_handler' => array('throw_exceptions' => false),
     *          ),
     *      ),
     * )
     * @var array
     */
    protected $options  = array();

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options  = array_merge($this->options, $options);
    }

    /**
     * Determine if we can create a service with name
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $cacheName  = $this->getCacheName($name, $requestedName);
        if ($cacheName) {
            return true;
        }
        return false;
    }

    /**
     * Create service with name
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $cacheName  = $this->getCacheName($name, $requestedName);
        $cache      = StorageFactory::factory($this->options[$cacheName]);
        return $cache;
    }

    /**
     * Checks if the requested name or alternatively the canonical name (in this order) exists in options as a cache
     * name and returns the first match. If neither name exists in options, returns null
     * @param $canonicalName
     * @param $requestedName
     * @return string|null
     */
    protected function getCacheName($canonicalName, $requestedName)
    {
        if (array_key_exists($requestedName, $this->options)) {
            return $requestedName;
        }
        if (array_key_exists($canonicalName, $this->options)) {
            return $canonicalName;
        }
        return null;
    }
}
