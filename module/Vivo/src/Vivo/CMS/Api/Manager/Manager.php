<?php
namespace Vivo\CMS\Api\Manager;

use Vivo\CMS\Model\Site;

/**
 * Bussiness claas for backend Manager.
 */

class Manager
{

    /**
     * Constructor.
     */
    public function __construct()
    {

    }

    /**
     * Returns sites that can be managed by current user.
     * @return Site[]
     */
    public function getManageableSites()
    {
        //TODO find real sites
        $site1 = new Site();
        $site2 = new Site();
        $site1->setPath('/sandbox');
        $site2->setPath('/sandbox2');
        return array ('sandbox' => $site1, 'sandbox2'=> $site2);
    }
}
