<?php
namespace Vivo\CMS\RefInt;

use Vivo\Repository\EventInterface as RepositoryEventInterface;

use Zend\EventManager\EventManagerInterface;

class Listener
{
    /**
     * Symbolic reference convertor
     * @var SymRefConvertor
     */
    protected $symRefConvertor;

    /**
     * Repository event manager
     * @var EventManagerInterface
     */
    protected $repositoryEvents;


    /**
     * Construct
     * @param EventManagerInterface $repositoryEvents
     */
    public function __construct(EventManagerInterface $repositoryEvents)
    {
        $this->repositoryEvents = $repositoryEvents;
        $repositoryEvents->attach(
            RepositoryEventInterface::EVENT_SERIALIZE_PRE, array($this, 'onRepositorySerializePre'));
    }

    /**
     * @param RepositoryEventInterface $event
     */
    public function onRepositorySerializePre(RepositoryEventInterface $event)
    {

    }
}
