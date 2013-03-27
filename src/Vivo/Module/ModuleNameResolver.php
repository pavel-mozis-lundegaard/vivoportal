<?php
namespace Vivo\Module;

/**
 * ModuleNameResolver
 * Resolves module name
 */
class ModuleNameResolver
{
    /**
     * Returns module name from fully qualified class name
     * Ie returns name of the module the class belongs to
     * @param string $fqcn
     */
    public function fromFqcn($fqcn)
    {
        $fqcn   = trim($fqcn, '\\');
        $parts  = explode('\\', $fqcn);
        $moduleName = $parts[0];
        return $moduleName;
    }
}