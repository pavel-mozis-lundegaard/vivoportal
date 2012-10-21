<?php
namespace Vivo\Site\Resolver;

/**
 * Map
 * Resolves site alias to site id using a map
 */
class Map implements ResolverInterface
{
    /**
     * Alias => Id map
     * @var array
     */
    protected $map  = array();

    /**
     * Constructor
     * @param array $map of alias => id pairs
     */
    public function __construct(array $map)
    {
        $this->map  = $map;
    }

    /**
     * Resolves the site alias to site id
     * If the alias cannot be resolved, returns false
     * @param string $alias
     * @return string|false
     */
    public function resolve($alias)
    {
        if (isset($this->map[$alias])) {
            $id = $this->map[$alias];
        } else {
            $id = false;
        }
        return $id;
    }
}