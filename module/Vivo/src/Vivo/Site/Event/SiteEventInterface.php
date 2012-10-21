<?php
namespace Vivo\Site\Event;

use Zend\EventManager\EventInterface;

/**
 * SiteEventInterface
 */
interface SiteEventInterface extends EventInterface
{
    const EVENT_INIT            = 'init';
    const EVENT_RESOLVE         = 'resolve';
    const EVENT_CONFIG          = 'config';
    const EVENT_LOAD_MODULES    = 'load_modules';
}