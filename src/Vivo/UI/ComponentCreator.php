<?php
namespace Vivo\UI;

use Zend\ServiceManager\ServiceManager;;
use Zend\Di\Di;
use Zend\EventManager\EventManager;

/**
 * ComponentCreator
 * Creates UI component by their name
 */
class ComponentCreator
{
    /**
     * Service manager responsible for creation of UI components
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Di container working as a fallback component factory
     * @var Di
     */
    protected $di;

    /**
     * EventManager
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Constructor
     * @param ServiceManager $sm
     * @param Di $di
     */
    public function __construct(ServiceManager $sm, Di $di = null)
    {
        $this->sm   = $sm;
        $this->di   = $di;
    }

    /**
     * Create new instance of component.
     * If service manager can create the component, SM is used. Otherwise DI is used.
     * @param string $name
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * @return \Vivo\UI\Component
     */
    public function createComponent($name)
    {
        if ($this->sm->has($name, false)) {
            //Create using SM
            $component = $this->sm->create($name);
            $type = 'ServiceManager';
        } elseif ($this->di) {
            //Create using DI
            $component = $this->di->newInstance($name, array(), false);
            $type = 'DI';
        } else {
            //Cannot create the component
            throw new Exception\InvalidArgumentException(
                sprintf("%s: No factory is able to create the component '%s'", __METHOD__, $name));
        }
        if (!$component instanceof ComponentInterface) {
            throw new Exception\RuntimeException(sprintf("%s: Object must be instance of ComponentInterface. Got '%s'",
                __METHOD__, get_class($component)));
        }
        $message = sprintf("Created component '%s' using '%s'", get_class($component), $type);
        $this->getEventManager()->trigger('log', $this, array(
            'message'   => $message,
            'priority'  => \VpLogger\Log\Logger::PERF_FINER));

        return $component;
    }

    /**
     * Returns EventManager
     * @return EventManager
     */
    public function getEventManager()
    {
        if (!$this->eventManager) {
            $this->eventManager = new EventManager();
        }
        return $this->eventManager;
    }
}
