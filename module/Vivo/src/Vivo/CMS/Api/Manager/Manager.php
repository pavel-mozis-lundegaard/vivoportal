<?php
namespace Vivo\CMS\Api\Manager;

use Vivo\Repository\Repository;

use Vivo\CMS\Api\CMS;

use Vivo\CMS\Model\Site;

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
     * Constructor.
     */
    public function __construct(CMS $cms, Repository $repository)
    {
        $this->cms = $cms;
        $this->repository = $repository;
    }

    /**
     * Returns sites that can be managed by current user.
     * @return Site[]
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
