<?php
namespace Vivo\CMS\Api;

use Vivo\Repository\Repository;
use Vivo\CMS\Model;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;
use Vivo\Indexer\QueryBuilder;

/**
 * Business class for managing sites.
 */
class Site
{
    /**
     * @var CMS
     */
    protected $cms;

    /**
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Indexer API
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Query Builder
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * Constructor.
     * @param CMS $cms
     * @param Repository $repository
     * @param IndexerInterface $indexerApi
     * @param \Vivo\Indexer\QueryBuilder $queryBuilder
     */
    public function __construct(CMS $cms,
                                Repository $repository,
                                IndexerApiInterface $indexerApi,
                                QueryBuilder $queryBuilder)
    {
        $this->cms          = $cms;
        $this->repository   = $repository;
        $this->indexerApi   = $indexerApi;
        $this->qb           = $queryBuilder;
    }

    /**
     * Returns sites that can be managed by current user.
     * @return Model\Site[]
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

    /**
     * Returns Site matching given hostname.
     * If no site matches the hostname, returns null
     * @param string $host
     * @return Model\Site|null
     */
    public function getSiteByHost($host)
    {
        $query      = $this->qb->cond($host, '\\hosts');
        $entities   = $this->indexerApi->getEntitiesByQuery($query, array('page_size' => 1));
        if (count($entities) == 1) {
            //Site found
            $site   = reset($entities);
        } else {
            //Site not found - fallback to traversing the repo (necessary for reindexing)
            $sites  = $this->cms->getChildren(new Model\Folder('/'));
            $site   = null;
            foreach ($sites as $siteIter) {
                /** @var $siteIter \Vivo\CMS\Model\Site */
                if (($siteIter instanceof Model\Site) and (in_array($host, $siteIter->getHosts()))) {
                    $site   = $siteIter;
                    break;
                }
            }
        }
        return $site;
    }

}
