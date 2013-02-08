<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\UI\Component;

/**
 * Tree of documents.
 */
class Tree extends Component
{
    protected $entityManager;


    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityManager->getEventManager()->attach('entitySet', array ($this, 'onEntityChange'));
    }

    public function onEntityChange()
    {
        //todo
    }

    /**
     *
     * @param string $relPath
     */
    public function set($relPath)
    {
        $this->entityManager->setEntityByRelPath($relPath);
    }
}
