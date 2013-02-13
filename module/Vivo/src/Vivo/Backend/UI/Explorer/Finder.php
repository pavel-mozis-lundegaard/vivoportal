<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\Alert;
use Vivo\UI\Component;

use Zend\EventManager\Event;

class Finder extends Component
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    protected $entity;
    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::init()
     */
    public function init()
    {
        $this->entity = $this->entityManager->getEntity();
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->view->entity = $this->entity;
        return parent::view();
    }

    /**
     * @param string $relPath
     */
    public function set($relPath)
    {
        try {
            $this->entityManager->setEntityByRelPath($relPath);
        } catch (\Vivo\Repository\Exception\EntityNotFoundException $e) {
            //TODO translate message
            $this->alert->addMessage('Path does not exist.', Alert::TYPE_ERROR);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityManager->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));
    }

    /**
     * Callback for entity change event.
     * @param Event $e
     */
    public function onEntityChange(Event $e)
    {
        $this->entity = $e->getParam('entity');
    }

    /**
     * Return current entity.
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets Alert component.
     * @param Alert $alert
     */
    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }
}
