<?php
namespace Vivo\InputFilter\Condition;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\Config;

/**
 * Class ConditionPluginManagerFactory
 * @package Vivo\InputFilter\Condition
 */
class ConditionPluginManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                         = $serviceLocator->get('config');
        $conditionPluginManagerConfig   = new Config($config['input_filter_conditions']);
        $pluginManager                  = new ConditionPluginManager($conditionPluginManagerConfig);
        return $pluginManager;
    }
}
