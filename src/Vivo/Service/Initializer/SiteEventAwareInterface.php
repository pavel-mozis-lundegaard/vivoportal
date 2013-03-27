<?php
namespace Vivo\Service\Initializer;

use Vivo\SiteManager\Event\SiteEvent;

/**
 * Interaface for injecting SiteEvent
 */
interface SiteEventAwareInterface
{
    public function setSiteEvent(SiteEvent $siteEvent);
}
