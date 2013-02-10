<?php
namespace Vivo\UI;

use Vivo\UI\Ribbon\Tab;
use Vivo\UI\Ribbon\Item;

use Zend\EventManager\EventManagerAwareInterface;

/**
 * Ribbon
 */
class Ribbon extends TabContainer implements EventManagerAwareInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var integer
     */
    protected $tabIndex = 0;

	/**
	 * (non-PHPdoc)
	 * @see \Vivo\UI\TabContainer::view()
	 */
	public function view() {
        $this->view->name = $this->getName();
        $this->view->components = array_keys($this->components);
        return parent::view();
    }

    /**
     * Trigges event after click on ribbon item.
     * @param unknown $name
     */
    public function itemClick($name)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('itemName' => $name));
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
    }

    /**
     * Adds ribbon tab.
     * @param Tab $tab
     */
    public function addTab(Tab $tab)
    {
        if ($name = $tab->getName()) {
            $this->addComponent($tab, $name);
        } else {
            $this->addComponent($tab, 'tab'.$this->tabIndex++);
        }
    }
}
