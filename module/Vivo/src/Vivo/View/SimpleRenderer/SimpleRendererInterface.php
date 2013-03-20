<?php
namespace Vivo\View\SimpleRenderer;

use Zend\View\Model\ModelInterface;

/**
 * Class SimpleRendererInterface
 * @package Vivo\View\SimplePhpRenderer
 */
interface SimpleRendererInterface
{
    /**
     * Renders template and returns the rendered output
     * @param string $template Template name
     * @param array $vars Array of variables to pass to the template
     * @param string $layout Layout template name
     * @param array $layoutVars Variables passed to the layout
     * @return string
     */
    public function renderTemplate($template, array $vars = array(), $layout = null, array $layoutVars = array());

    /**
     * Renders the view model and returns the rendered output
     * Use this method to render more complicated models (eg with children)
     * @param ModelInterface $model
     * @return string
     */
    public function renderViewModel(ModelInterface $model);
}
