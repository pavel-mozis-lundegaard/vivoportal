<?php
namespace Vivo\Site;

/**
 * SiteInterface
 */
interface SiteInterface
{
    /**
     * Sets the Site ID
     * @param string $siteId
     */
    public function setSiteId($siteId);

    /**
     * Returns the Site ID
     * @return string
     */
    public function getSiteId();

    /**
     * Sets the current Site alias
     * @param string $siteAlias
     */
    public function setSiteAlias($siteAlias);

    /**
     * Returns the current Site alias
     * @return string
     */
    public function getSiteAlias();

    /**
     * Sets the site configuration
     * @param array|\ArrayAccess $config
     * @return void
     */
    public function setConfig($config);

    /**
     * Returns the Site configuration
     * @return array|\ArrayAccess
     */
    public function getConfig();

    /**
     * Sets the module names required by this Site
     * @param array $modules
     */
    public function setModules(array $modules);

    /**
     * Returns the module names required by this site
     * @return array
     */
    public function getModules();
}