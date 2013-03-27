<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * PhtmlRenderingStrategyFactory
 */
class PhtmlRenderingStrategyFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $parser = new \Vivo\Util\Path\PathParser();
        $resolver = new \Vivo\View\Resolver\TemplateResolver(
                $serviceLocator->get('module_resource_manager'), $parser,
                $config['vivo']['templates']);
        $renderer = new \Vivo\View\Renderer\PhtmlRenderer();
        $renderer->setResolver($resolver);
        $renderer->setHelperPluginManager($serviceLocator->get('ViewHelperManager'));
        $strategy = new \Vivo\View\Strategy\PhtmlRenderingStrategy($renderer, $resolver);
        return $strategy;
    }
}
