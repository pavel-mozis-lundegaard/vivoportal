<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab Group Item
 */
class Item extends UI\Component implements UI\TabContainerItemInterface, UI\RibbonItemInterface
{

    private $icon;
    private $visible = true;
    private $active = false;
    private $enabled = true;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var Ribbon
     */
    protected $ribbon;

    /**
     * Constructor
     * @param string $name
     * @param string $label
     * @param string $icon
     * @param Ribbon $ribbon
     */
    public function __construct($name, $label, $icon, Ribbon $ribbon = null)
    {
        $this->setName($name);
        $this->label = $label;
        $this->icon = $icon;
        $this->setRibbon($ribbon);
    }

    /**
     * Sets ribbon.
     * @param Ribbon $ribbon
     */
    public function setRibbon(Ribbon $ribbon)
    {
        $this->ribbon = $ribbon;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\TabContainerItemInterface::getLabel()
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @param string $label
     */
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

    /**
     * Called when clicked on ribbon item.
     */
    public function click()
    {
        $this->ribbon->itemClick($this->getName());
    }

    public function select()
    {
        $this->setActive(true);
    }

    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->getView()->icon = $this->getIcon();
        $this->getView()->enabled = $this->isEnabled();
        $this->getView()->label = $this->label;
        $this->getView()->visible = $this->isVisible();
        $this->getView()->active = $this->isActive();
        return parent::view();
    }
}