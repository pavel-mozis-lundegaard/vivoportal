<?php
namespace Vivo\CMS\Api;

use Vivo\Repository\Repository;
use Vivo\CMS\Model;

/**
 * Bussiness claas for managing sites.
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
     * Constructor.
     * @param CMS $cms
     * @param Repository $repository
     */
    public function __construct(CMS $cms, Repository $repository)
    {
        $this->cms = $cms;
        $this->repository = $repository;
    }

    /**
     * Returns sites that can be managed by current user.
     * @return Model\Site[]
     */
    public function getManageableSites()
    {
        //TODO security
        $query = '\path:"/*" AND \class:"Vivo\CMS\Model\Site"';
        $sites = $this->repository->getEntities($query);
        $result = array();
        foreach ($sites as $site) {
            $result[$site->getName()] = $site;
        }
        return $result;
    }
}
