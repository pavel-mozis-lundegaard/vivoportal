<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab Group
 */
class Group extends UI\ComponentContainer
{

    /**
     * @var string
     */
    protected $label;

    /**
     * Constructor.
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::init()
     */
    public function init()
    {
        $items = array();
        foreach($this->components as $component) {
            if($component->isVisible()) {
                $items[] = $component->getName();
            }
        }
        $this->getView()->components = $items;
        $this->getView()->label = $this->label;

        parent::init();
    }

    /**
     * Adds ribbon item to group.
     * @param Item $item
     * @return \Vivo\UI\Ribbon\Group
     */
    public function addItem(Item $item)
    {
        $this->addComponent($item, $item->getName());
    }
}
