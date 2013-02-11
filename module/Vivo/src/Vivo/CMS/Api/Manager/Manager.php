<?php
namespace Vivo\CMS\Api\Manager;

use Vivo\Repository\Repository;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Site;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;

/**
 * Business class for backend Manager.
 */

class Manager
{

    protected $cms;

    /**
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Indexer Api
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\Repository\Repository $repository
     * @param \Vivo\CMS\Api\IndexerInterface $indexerApi
     */
    public function __construct(CMS $cms, Repository $repository, IndexerApiInterface $indexerApi)
    {
        $this->cms          = $cms;
        $this->repository   = $repository;
        $this->indexerApi   = $indexerApi;
    }

    /**
     * Returns sites that can be managed by current user.
     * @return Site[]
     */
    public function getManageableSites()
    {
        //TODO security
        $query = '\path:"/*" AND \class:"Vivo\CMS\Model\Site"';
        $sites = $this->indexerApi->getEntitiesByQuery($query);
        $result = array();
        foreach ($sites as $site) {
            $result[$site->getName()] = $site;
        }
        return $result;
    }
}
