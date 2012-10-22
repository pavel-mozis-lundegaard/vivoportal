<?php
namespace Vivo\SiteManager\Resolver;

/**
 * ResolverInterface
 */
interface ResolverInterface
{
    /**
     * Resolves the host name to site id
     * If the host name cannot be resolved, returns false
     * @param string $host
     * @return string|false
     */
    public function resolve($host);
}