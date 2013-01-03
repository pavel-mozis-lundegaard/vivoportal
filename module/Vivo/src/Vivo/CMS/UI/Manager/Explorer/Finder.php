<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\Manager\SiteSelector;
use Vivo\UI\Component;

use Zend\EventManager\Event;

class Finder extends Component
{

    private $entityManager;
    private $entity;

    public function init()
    {

        $this->entity = $this->getParent()->getEntity();
    }

    public function view()
    {
        $this->view->entity = $this->entity;
        return parent::view();
    }

    public function onEntityChange(Event $e)
    {
        $this->entity = $e->getParam('entity');
    }

    public function set($relPath)
    {
        $this->entityManager->setEntityByRelPath($relPath);
    }

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityManager->getEventManager()->attach('entitySet', array ($this, 'onEntityChange'));
    }

}
