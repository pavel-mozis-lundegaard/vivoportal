<?php
namespace Vivo\Mail\View;

use Vivo\Mail\Exception;

use Vivo\View\SimpleRenderer\SimplePhpRenderer;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\View\Resolver\TemplateMapResolver;

/**
 * RendererFactory
 */
class SimpleRendererFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config             = $serviceLocator->get('cms_config');
        if (!isset($config['mail']['simple_renderer']['options'])) {
            throw new Exception\ConfigException(
                sprintf("%s: Key ['mail']['simple_renderer']['options'] missing in cms config", __METHOD__));
        }
        $options    = $config['mail']['simple_renderer']['options'];
        $mainHelperPluginManager    = $serviceLocator->get('view_helper_manager');
        $simpleRenderer = new SimplePhpRenderer($options, $mainHelperPluginManager);
        return $simpleRenderer;
    }
}
