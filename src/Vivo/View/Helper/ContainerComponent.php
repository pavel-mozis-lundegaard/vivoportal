<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ModelInterface as ViewModelInterface;

/**
 * ContainerComponent
 * Returns rendered component from component container
  */
class ContainerComponent extends AbstractHelper
{
    /**
     * Invokes the view helper as renderer method
     * @param int|string|null $componentId Int = component number, String = component name, null = return this helper
     * @param mixed $viewModel If null, uses the current view model
     * @return ContainerComponent|string
     */
    public function __invoke($componentId = null, $viewModel = null)
    {
        if (is_null($componentId)) {
            return $this;
        }
        return $this->render($componentId, $viewModel);
    }

    /**
     * Returns rendered component with the specified ID
     * @param int|string $componentId Int = component number, String = component name
     * @param mixed $viewModel If null, uses the current view model
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function render($componentId, $viewModel = null)
    {
        if (!$viewModel) {
            $viewModel  = $this->getView();
        }
        if (!$this->containerExists($viewModel)) {
            //The view model does not contain a container
            return sprintf("<!-- %s: Container not found in view -->", __METHOD__);
        }
        $components = $viewModel->container['components'];
        if ($this->componentExists($componentId, $viewModel)) {
            //Component found
            if (is_int($componentId)) {
                //Component ID specified as integer, get component at this position
                $rendered   = sprintf("\n<!-- Component number %s -->\n", $componentId);
                $rendered   .= $viewModel->{$components[$componentId]};
            } else {
                //Content ID specified as string, get content with this name
                $rendered   = sprintf("\n<!-- Component '%s' -->\n", $componentId);
                $rendered   .= $viewModel->{$componentId};
            }
        } else {
            //Component not found
            $rendered   = sprintf("<!-- %s: Component '%s' not found -->", __METHOD__, $componentId);
        }
        return $rendered;
    }

    /**
     * Renders all components sequentially
     * @param mixed $viewModel
     * @return string
     */
    public function renderAll($viewModel = null)
    {
        if (!$viewModel) {
            $viewModel  = $this->getView();
        }
        if (!isset($viewModel->container['components'])) {
            //The view does not contain the 'container' variable
            return sprintf("<!-- %s: Container not found in view -->", __METHOD__);
        }
        $components = $viewModel->container['components'];
        $rendered   = sprintf("\n<!-- Rendering all components in container -->\n");
        foreach ($components as $componentNumber => $componentName) {
            $rendered   .= sprintf("<!-- Component %s, '%s' -->\n", $componentNumber, $componentName);
            $rendered   .= $viewModel->{$componentName} . "\n";
        }
        return $rendered;
    }

    /**
     * Returns if component container exists in the view model
     * @param mixed If null, uses the current view model
     * @return bool
     */
    public function containerExists($viewModel = null)
    {
        if (!$viewModel) {
            $viewModel  = $this->getView();
        }
        if (isset($viewModel->container['components'])) {
            //A container exists in the view
            $retVal = true;
        } else {
            //The view does not contain the 'container' variable
            $retVal = false;
        }
        return $retVal;
    }

    /**
     * Returns if the specified component exists in the component container
     * @param int|string $componentId
     * @param mixed $viewModel If null, uses the current view model
     * @return bool
     */
    public function componentExists($componentId, $viewModel = null)
    {
        if (!$viewModel) {
            $viewModel  = $this->getView();
        }
        if (!$this->containerExists($viewModel)) {
            return false;
        }
        $components = $viewModel->container['components'];
        if (is_int($componentId) && array_key_exists($componentId, $components)) {
            return true;
        }
        if (is_string($componentId) && in_array($componentId, $components)) {
            return true;
        }
        return false;
    }

    /**
     * Returns an array with names of the container components
     * @param mixed $viewModel
     * @throws Exception\RuntimeException
     * @return array
     */
    public function getContainerComponentNames($viewModel = null)
    {
        if (!$this->containerExists($viewModel)) {
            throw new Exception\RuntimeException(sprintf("%s: Component container does not exist in view", __METHOD__));
        }
        if (!$viewModel) {
            $viewModel  = $this->getView();
        }
        $components = $viewModel->container['components'];
        return $components;
    }
}
