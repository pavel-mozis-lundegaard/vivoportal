<?php
namespace Vivo\Site\Resolver;

/**
 * ResolverInterface
 */
interface ResolverInterface
{
    /**
     * Resolves the site alias to site id
     * If the alias cannot be resolved, returns false
     * @param string $alias
     * @return string|false
     */
    public function resolve($alias);
}