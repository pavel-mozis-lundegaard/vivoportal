<?php
namespace Vivo\Module\Feature;

use Vivo\CMS\Model\Site;
use Vivo\CMS\Api\CMS;
use Vivo\Service\DbServiceManagerInterface;

/**
 * SiteUninstallableInterface
 * Classes implementing this interface provide uninstallation script for module uninstallation from a site
 */
interface SiteUninstallableInterface
{
    /**
     * Runs uninstallation script
     * @param string $moduleName
     * @param string $siteName
     * @param Site $site
     * @param CMS $cms
     * @param DbServiceManagerInterface $dbServiceManager
     * @param string $dbSource Name of the db source
     * @return void
     */
    public function uninstall($moduleName, $siteName, Site $site, CMS $cms,
                            DbServiceManagerInterface $dbServiceManager, $dbSource);
}