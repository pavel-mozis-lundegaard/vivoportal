<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Vivo\UI\Component;

class Ribbon extends Component implements EventManagerAwareInterface
{
    private $eventManager;

    public function __construct()
    {

    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function itemClick($name)
    {
        $this->eventManager->trigger(__FUNCTION__, $this, array('itemName' => $name));
    }
}
