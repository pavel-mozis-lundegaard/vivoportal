<?php
namespace Vivo\CMS\Api;

use Vivo\Repository\RepositoryInterface as VivoRepository;

/**
 * Repository API
 */
class Repository
{
    /**
     * Vivo Repository
     * @var VivoRepository
     */
    protected $repository;

    /**
     * Constructor
     * @param \Vivo\Repository\RepositoryInterface $repository
     */
    public function __construct(VivoRepository $repository)
    {
        $this->repository   = $repository;
    }

    /**
     * Returns an array of duplicate uuids
     * array(
     *  'uuid1' => array(
     *      'path1',
     *      'path2',
     *  ),
     *  'uuid2' => array(
     *      'path3',
     *      'path4',
     *  ),
     * )
     * @param string $path
     * @return array
     */
    public function getDuplicateUuids($path)
    {
        return $this->repository->getDuplicateUuids($path);
    }
}
