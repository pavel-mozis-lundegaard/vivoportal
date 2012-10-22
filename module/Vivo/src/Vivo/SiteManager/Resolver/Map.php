<?php
namespace Vivo\SiteManager\Resolver;

/**
 * Map
 * Resolves host name to site id using a map
 */
class Map implements ResolverInterface
{
    /**
     * Host => Id map
     * @var array
     */
    protected $map  = array();

    /**
     * Constructor
     * @param array $map of host => id pairs
     */
    public function __construct(array $map)
    {
        $this->map  = $map;
    }

    /**
     * Resolves the host name to site id
     * If the host name cannot be resolved, returns false
     * @param string $host
     * @return string|false
     */
    public function resolve($host)
    {
        if (isset($this->map[$host])) {
            $id = $this->map[$host];
        } else {
            $id = false;
        }
        return $id;
    }
}