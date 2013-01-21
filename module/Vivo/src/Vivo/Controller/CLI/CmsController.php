<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\CMS;
use Vivo\SiteManager\Event\SiteEvent;

/**
 * Vivo CLI controller for command 'repository'
 */
class CmsController extends AbstractCliController
{
    const COMMAND = 'cms';

    /**
     * CMS Api
     * @var CMS
     */
    protected $cms;

    /**
     * SiteEvent
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     */
    public function __construct(CMS $cms, SiteEvent $siteEvent)
    {
        $this->cms              = $cms;
        $this->siteEvent        = $siteEvent;
    }

    public function getConsoleUsage()
    {
        return 'CMS usage: ...';
    }

    public function reindexAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $host   = $request->getParam('host');
        $path   = $request->getParam('path');
        if (!$this->siteEvent->getSite()) {
            $output = sprintf("No site object created; host = '%s', path = '%s'", $host, $path);
            return $output;
        }

        $numIndexed = $this->cms->reindex($path, true);
        $output = sprintf("%s items reindexed", $numIndexed);
        return $output;
    }

    public function helpAction()
    {
        $output = 'cms reindex <host> <full_path>';
        return $output;
    }
}
