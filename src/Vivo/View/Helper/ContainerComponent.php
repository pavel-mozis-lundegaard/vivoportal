<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * ContainerComponent
 * Returns rendered component from component container
 * This view helper is usable only in view templates of Vivo\UI\ComponentContainer
 */
class ContainerComponent extends AbstractHelper
{
    /**
     * Invokes the view helper as renderer method
     * @param int|string|null $componentId Int = component number, String = component name, null = return this helper
     * @param string|null $defaultContent Default content displayed when the specified component is not available
     * NOTE: the $defaultContent must be already escaped!
     * @return ContainerComponent|string
     */
    public function __invoke($componentId = null, $defaultContent = null)
    {
        if (is_null($componentId)) {
            return $this;
        }
        return $this->render($componentId, $defaultContent);
    }

    /**
     * Returns rendered component with the specified ID
     * @param int|string $componentId Int = component number, String = component name
     * @param string|null $defaultContent Default content displayed when the specified component is not available
     * NOTE: the $defaultContent must be already escaped!
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function render($componentId, $defaultContent = null)
    {
        if (!isset($this->getView()->container['components'])) {
            //The view does not contain the 'container' variable, probably not called from ComponentContainer template
            return sprintf("<!-- %s: Container not found in view -->", __METHOD__);
        }
        $components = $this->getView()->container['components'];
        if (is_int($componentId)) {
            //Component ID specified as integer, get component at this position
            if (array_key_exists($componentId, $components)) {
                //Component found
                $rendered   = sprintf("\n<!-- Component number %s -->\n", $componentId);
                $rendered   .= $this->getView()->{$components[$componentId]};
            } elseif ($defaultContent) {
                //Component not found, but we have default content to display
                $rendered   = sprintf("\n<!-- Component number %s not found, using default content -->\n",
                                    $componentId);
                $rendered   .= $defaultContent;
            } else {
                //Component not found and default content is not set
                $rendered   = sprintf('<!-- %s: Component number %s not found -->', __METHOD__, $componentId);
            }
        } elseif (is_string($componentId)) {
            //Content ID specified as string, get content with this name
            if (in_array($componentId, $components)) {
                //Component found
                $rendered   = sprintf("\n<!-- Component '%s' -->\n", $componentId);
                $rendered   .= $this->getView()->{$componentId};
            } elseif ($defaultContent) {
                //Component not found, but we have default content to display
                $rendered   = sprintf("\n<!-- Component '%s' not found, using default content -->\n",
                                    $componentId);
                $rendered   .= $defaultContent;
            } else {
                //Component not found and default content is not set
                $rendered   = sprintf("<!-- %s: Component '%s' not found -->", __METHOD__, $componentId);
            }
        } else {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: contentId must be either integer or string", __METHOD__));
        }
        return $rendered;
    }

    /**
     * Renders all components sequentially
     * @return string
     */
    public function renderAll()
    {
        if (!isset($this->getView()->container['components'])) {
            //The view does not contain the 'container' variable, probably not called from ComponentContainer template
            return sprintf("<!-- %s: Container not found in view -->", __METHOD__);
        }
        $components = $this->getView()->container['components'];
        $rendered   = sprintf("\n<!-- Rendering all components in container -->\n");
        foreach ($components as $componentNumber => $componentName) {
            $rendered   .= sprintf("<!-- Component %s, '%s' -->\n", $componentNumber, $componentName);
            $rendered   .= $this->getView()->{$componentName} . "\n";
        }
        return $rendered;
    }
}
