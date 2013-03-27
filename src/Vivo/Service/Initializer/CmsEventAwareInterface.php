<?php
namespace Vivo\Service\Initializer;

use Vivo\CMS\Event\CMSEvent;

/**
 * Class CmsEventAwareInterface
 * @package Vivo\CMS\UI
 */
interface CmsEventAwareInterface
{
    /**
     * Sets the CMS event
     * @param CMSEvent $cmsEvent
     * @return void
     */
    public function setCmsEvent(CMSEvent $cmsEvent);
}
