<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * LogicalContent
 * Returns rendered logical content
 * This view helper is usable only in view templates of Vivo\UI\ComponentContainer
 */
class LogicalContent extends AbstractHelper
{
    /**
     * Invokes the view helper as renderer method
     * @param int|string|null $contentId Int = content number, String = content name, null = return this view helper
     * @return LogicalContent|string
     */
    public function __invoke($contentId = null)
    {
        if (is_null($contentId)) {
            return $this;
        }
        return $this->render($contentId);
    }

    /**
     * Renders content with the specified id
     * @param int|string $contentId Int = content number, String = content name
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function render($contentId)
    {
        if (!isset($this->getView()->container['components'])) {
            //The view does not contain the 'container' variable, probably not called from ComponentContainer template
            return '';
        }
        $components = $this->getView()->container['components'];
        if (is_int($contentId)) {
            //Content ID specified as integer, get content at this position
            if (!array_key_exists($contentId, $components)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Logical content at position %s does not exist", __METHOD__, $contentId));
            }
            $rendered   = $this->getView()->{$components[$contentId]};
        } elseif (is_string($contentId)) {
            //Content ID specified as string, get content with this name
            if (!in_array($contentId, $components)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Logical content with name '%s' does not exist", __METHOD__, $contentId));
            }
            $rendered   = $this->getView()->{$contentId};
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
            return '';
        }
        $components = $this->getView()->container['components'];
        $rendered   = '';
        foreach ($components as $contentNumber => $componentName) {
            $rendered   .= sprintf("<!-- Logical content %s, '%s' -->\n", $contentNumber, $componentName);
            $rendered   .= $this->getView()->{$componentName} . "\n";
        }
        return $rendered;
    }
}
