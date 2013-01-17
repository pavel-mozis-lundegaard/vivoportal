<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\Repository as RepositoryApi;

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
     * Constructor
     * @param \Vivo\CMS\Api\Repository $repositoryApi
     */
    public function __construct(RepositoryApi $repositoryApi)
    {
        $this->repositoryApi    = $repositoryApi;
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
