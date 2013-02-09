<?php
namespace Vivo\Repository;

use Zend\EventManager\EventInterface as ZendEventInterface;

/**
 * EventInterface
 * Repository event interface
 */
interface EventInterface extends ZendEventInterface
{
    /**
     * Event triggered right before the repository commit finishes
     */
    const EVENT_COMMIT      = 'commit';
}
