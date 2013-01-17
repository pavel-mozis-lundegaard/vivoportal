<?php
namespace Vivo\CMS\Api;

use Vivo\Repository\Repository as VivoRepository;
use Vivo\CMS\Model\Site;

/**
 * Repository
 * Repository API
 */
class Repository
{
    /**
     * Repository
     * @var VivoRepository
     */
    protected $repository;

    /**
     * Constructor
     * @param \Vivo\Repository\Repository $repository
     */
    public function __construct(VivoRepository $repository)
    {
        $this->repository   = $repository;
    }

    /**
     * Reindexes the given path and returns the number of reindexed items
     * @param string $path
     * @return int
     */
    public function reindex($path)
    {
        return $this->repository->reindex($path, true);
    }
}