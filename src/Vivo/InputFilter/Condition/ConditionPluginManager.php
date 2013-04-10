<?php
namespace Vivo\InputFilter\Condition;

use Vivo\InputFilter\Exception;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * ConditionPluginManager
 */
class ConditionPluginManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default; default to false
     * @var bool
     */
    protected $shareByDefault           = false;

    /**
     * Whether or not to auto-add a class as an invokable class if it exists
     * @var bool
     */
    protected $autoAddInvokableClass    = false;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached translator, if any, to the currently requested helper.
     *
     * @param  null|ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
//        $this->addInitializer(array($this, 'injectTranslator'));
    }

    /**
     * Validate the plugin
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ConditionInterface) {
            // we're okay
            return;
        }
        throw new Exception\RuntimeException(
            sprintf('%s: Plugin of type %s is invalid; must implement ConditionInterface',
                __METHOD__, (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

}
