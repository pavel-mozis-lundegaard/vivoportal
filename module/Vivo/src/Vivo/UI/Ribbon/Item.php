<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab Group Item
 * 
 * @author peter.krajcar
 */
class Item extends UI\Component implements UI\TabContainerItemInterface, UI\RibbonItemInterface
{
    const SMALL = 16;
    const NORMAL = 24;
    const LARGE = 48;

    private $icon;
    private $size;
    private $handler;
    private $visible = true;
    private $active = false;
    private $enabled = true;

    public function __construct($name, $label, $icon, $size = self::NORMAL, $handler = NULL)
    {
        $this->name = $name;
        $this->label = $label;
        $this->icon = $icon;
        $this->size = $size;
        $this->setHandler($handler);
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible($visible)
    {
        $this->visible = (bool) $visible;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    function invoke()
    {
        $handler = $this->getHandler();
        if (is_object($handler)) {
            if ($handler instanceof \Closure) {
                $closure = $handler;
                $closure($this);
            } elseif ($handler instanceof UI\Ribbon\Handler) {
                $handler->invoke($this);
            }
        } elseif (is_array($handler)) {
            $handler[0]->{$handler[1]}($this);
        }
    }

    public function select()
    {
        $this->setActive(true);
    }

    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    public function init() {
        $this->view->name = $this->getName();
        $this->view->icon = $this->getIcon();
        $this->view->size = $this->getSize();
        $this->view->enabled = $this->isEnabled();
    }
}