<?php
namespace Vivo\Site\Event;

use Zend\EventManager\EventInterface;

/**
 * SiteEventInterface
 */
interface SiteEventInterface extends EventInterface
{
    const EVENT_INIT        = 'init';
    const EVENT_BOOTSTRAP   = 'bootstrap';
}