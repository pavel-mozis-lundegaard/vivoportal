<?php
namespace Vivo\Log;

use Zend\Log\WriterPluginManager;
use Zend\ServiceManager\Config as SmConfig;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class WriterPluginManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('config');
        if (isset($config['logger']['writer_plugin_manager'])) {
            $pluginsConfig  = new SmConfig($config['logger']['writer_plugin_manager']);
        } else {
            $pluginsConfig  = null;
        }
        $pluginManager  = new WriterPluginManager($pluginsConfig);
        return $pluginManager;
    }
}
