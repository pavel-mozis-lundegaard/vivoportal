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
}
