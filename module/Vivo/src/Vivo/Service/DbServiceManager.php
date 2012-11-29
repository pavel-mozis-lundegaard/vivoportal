<?php
namespace Vivo\Service;

use Zend\ServiceManager\AbstractPluginManager;

use Zend\ServiceManager\Exception\RuntimeException;

use PDO;

/**
 * DbServiceManager
 * Provides DB services
 */
class DbServiceManager extends AbstractPluginManager
{
    /**
     * Whether or not to auto-add a class as an invokable class if it exists
     * @var bool
     */
    protected $autoAddInvokableClass = false;

    /**
     * Validate the plugin
     * @param  mixed $plugin
     * @return void
     * @throws RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof PDO) {
            // we're okay
            return;
        }
        throw new RuntimeException(sprintf(
            '%s: DB service of type %s is invalid; must be either a \PDO or Doctrine EntityManager',
            __METHOD__, (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}