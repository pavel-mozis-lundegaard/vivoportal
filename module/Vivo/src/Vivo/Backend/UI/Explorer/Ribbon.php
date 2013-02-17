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
     * Constructor.
     */
    public function __construct()
    {
        $tab = new Tab('Document');

        $group = new Group('Show');
        $group->addItem(new Item('viewer', 'Viewer', '', $this));
        $group->addItem(new Item('browser', 'Browser', '', $this));
        $tab->addGroup($group);

        $group = new Group('Edit');
        $group->addItem(new Item('editor', 'Editor', '', $this));
        $tab->addGroup($group);

        $this->addTab($tab);
        $tab = new Tab('Advanced');
        $this->addTab($tab);

        $group = new Group('Expert');
        $group->addItem(new Item('inspect', 'Inspect', '', $this));
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
}
