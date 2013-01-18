<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\Repository as RepositoryApi;
use Vivo\SiteManager\Event\SiteEvent;

/**
 * Vivo CLI controller for command 'repository'
 */
class RepositoryController extends AbstractCliController
{
    const COMMAND = 'repository';

    /**
     * Repository API
     * @var RepositoryApi;
     */
    protected $repositoryApi;

    /**
     * SiteEvent
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\Repository $repositoryApi
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     */
    public function __construct(RepositoryApi $repositoryApi, SiteEvent $siteEvent)
    {
        $this->repositoryApi    = $repositoryApi;
        $this->siteEvent        = $siteEvent;
    }

    public function getConsoleUsage()
    {
        return 'Repository usage: ...';
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

        $numIndexed = $this->repositoryApi->reindex($path);
        $output = sprintf("%s items reindexed", $numIndexed);
        return $output;
    }

    public function helpAction()
    {
        $output = 'repository reindex <host> <full_path>';
        return $output;
    }
}
