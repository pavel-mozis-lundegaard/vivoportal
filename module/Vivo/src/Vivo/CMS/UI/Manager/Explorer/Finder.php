<?php
namespace Vivo\CMS\UI\Manager\Explorer;

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
        $this->entityManager->setEntityByRelPath($relPath);
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
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
}
