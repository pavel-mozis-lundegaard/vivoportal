<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\Ribbon\Tab;
use Vivo\UI\Ribbon\Group;
use Vivo\UI\Ribbon\Item;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Ribbon for explorer.
 *
 */
class Ribbon extends \Vivo\UI\Ribbon implements EventManagerAwareInterface
{
    /**
     * Array of all ribbon items
     * @var Item[]
     */
    protected $items    = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setViewAll(true);

        $tab = new Tab('Document');
        $this->addTab($tab);

        $group = new Group('Show');
        //Viewer
        $item     = new Item('viewer', 'View', '', $this);
        $this->items[$item->getName()]    = $item;
        $group->addItem($item);
        //Browser
        $item    = new Item('browser', 'Browse', '', $this);
        $this->items[$item->getName()]    = $item;
        $group->addItem($item);
        $tab->addGroup($group);

        $group  = new Group('Editor');
        //Editor
        $item = new Item('editor', 'Edit', '', $this);
        $this->items[$item->getName()]    = $item;
        $group->addItem($item);
        $tab->addGroup($group);

        $group = new Group('Structure');
        //Creator
        $item   = new Item('creator', 'Create', '', $this);
        $this->items[$item->getName()]  = $item;
        $group->addItem($item);
        //Copy
        $item   = new Item('copy', 'Copy', '', $this);
        $this->items[$item->getName()]  = $item;
        $group->addItem($item);
        //Move
        $item   = new Item('move', 'Move', '', $this);
        $this->items[$item->getName()]  = $item;
        $group->addItem($item);
        //Delete
        $item   = new Item('delete', 'Delete', '', $this);
        $this->items[$item->getName()]  = $item;
        $group->addItem($item);
        $tab->addGroup($group);

        $tab = new Tab('Advanced');
        $this->addTab($tab);

        $group  = new Group('Expert');
        $item   = new Item('inspect', 'Inspect', '', $this);
        $this->items[$item->getName()]  = $item;
        $group->addItem($item);
        $tab->addGroup($group);
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::getDefaultTemplate()
     */
    public function getDefaultTemplate()
    {
        //use parent template
        return get_parent_class($this);
    }

    /**
     * Deactivates all ribbon items
     */
    public function deactivateAll()
    {
        foreach ($this->items as $item) {
            $item->setActive(false);
        }
    }

    /**
     * Sets the specified item as active
     * @param string $name Item name
     * @throws Exception\InvalidArgumentException
     */
    public function setActive($name)
    {
        if (!array_key_exists($name, $this->items)) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Item '%s' does not exist", __METHOD__, $name));
        }
        $item   = $this->items[$name];
        $item->select();
    }
}
