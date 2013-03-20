<?php
namespace Vivo\View\SimpleRenderer;

use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class PhpRenderer
 * This simple renderer is essentially just a wrapper around the Zend\View\Renderer\PhpRenderer
 * facilitating its use
 * @package Vivo\View\SimpleRenderer
 */
class SimplePhpRenderer implements SimpleRendererInterface
{
    /**
     * PHP renderer
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * Options
     * @var array
     */
    protected $options  = array(
        //Name of variable in the layout model containing the actual content
        'capture_content_to'    => 'content',
        //Map of template names mapped to phtml files
        'template_map'          => array(),
        //Array of helper names to be copied from the main helper plugin manager
        //Note that there are Zend view helpers available by default
        //See Zend\View\HelperPluginManager
        'use_helpers'           => array(),
    );

    /**
     * Constructor
     * @param array $options
     * @param \Zend\ServiceManager\AbstractPluginManager $mainHelperPluginManager
     */
    public function __construct(array $options = array(), AbstractPluginManager $mainHelperPluginManager = null)
    {
        $this->options      = array_merge($this->options, $options);
        $resolver           = new TemplateMapResolver($options['template_map']);
        $this->renderer     = new PhpRenderer();
        $this->renderer->setResolver($resolver);
        $pluginManager      = $this->renderer->getHelperPluginManager();
        if ($mainHelperPluginManager && !empty($this->options['use_helpers'])) {
            foreach ($this->options['use_helpers'] as $helperName) {
                $helper = $mainHelperPluginManager->get($helperName);
                $pluginManager->setService($helperName, $helper);
            }
        }
    }

    /**
     * Renders template and returns the rendered output
     * @param string $template Template name
     * @param array $vars Array of variables to pass to the template
     * @param string $layout Layout template name
     * @param array $layoutVars Variables passed to the layout
     * @return string
     */
    public function renderTemplate($template, array $vars = array(), $layout = null, array $layoutVars = array())
    {
        $viewModel  = new ViewModel();
        $viewModel->setTemplate($template);
        $viewModel->setVariables($vars);
        $modelToRender  = $viewModel;
        if ($layout) {
            $layoutModel    = new ViewModel();
            $layoutModel->setTemplate($layout);
            $layoutModel->setVariables($layoutVars);
            $layoutModel->addChild($viewModel, $this->options['capture_content_to']);
            $modelToRender  = $layoutModel;
        }
        $output     = $this->renderViewModel($modelToRender);
        return $output;
    }

    /**
     * Renders the view model and returns the rendered output
     * Use this method to render more complicated models with children
     * @param ModelInterface $model
     * @return string
     */
    public function renderViewModel(ModelInterface $model)
    {
        if ($model->hasChildren()) {
            $this->renderChildren($model);
        }
        $rendered   = $this->renderer->render($model);
        return $rendered;
    }

    /**
     * Loop through children, rendering each
     * @param  ModelInterface $model
     * @throws Exception\DomainException
     * @return void
     */
    protected function renderChildren(ModelInterface $model)
    {
        /** @var $child ViewModel */
        foreach ($model as $child) {
            if ($child->terminate()) {
                throw new Exception\DomainException('Inconsistent state; child view model is marked as terminal');
            }
            $child->setOption('has_parent', true);
            $result  = $this->renderViewModel($child);
            $child->setOption('has_parent', null);
            $capture = $child->captureTo();
            if (!empty($capture)) {
                if ($child->isAppend()) {
                    $oldResult=$model->{$capture};
                    $model->setVariable($capture, $oldResult . $result);
                } else {
                    $model->setVariable($capture, $result);
                }
            }
        }
    }
}
