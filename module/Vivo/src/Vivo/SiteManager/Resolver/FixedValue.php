<?php
namespace Vivo\SiteManager\Resolver;

/**
 * FixedValue
 * Resolves any host name to a fixed site id
 */
class FixedValue implements ResolverInterface
{
    /**
     * Fixed value for site id
     * @var string
     */
    protected $siteId;

    /**
     * Constructor
     * @param string $siteId
     */
    public function __construct($siteId)
    {
        $this->siteId   = $siteId;
    }

    /**
     * Resolves the host name to site id
     * If the host name cannot be resolved, returns false
     * @param string $host
     * @return string|false
     */
    public function resolve($host)
    {
        return $this->siteId;
    }
}