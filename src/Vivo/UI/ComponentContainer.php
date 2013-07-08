<?php
namespace Vivo\UI;

use Vivo\UI\Exception\ComponentNotExists;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ModelInterface;

/**
 * @todo implement ArrayAccess?
 */

class ComponentContainer extends Component implements ComponentContainerInterface
{

    /**
     * Child components
     * @var ComponentInterface[]
     */
    protected $components = array();

    /**
     * Returns child component by name
     * @param string $name
     * @return ComponentInterface
     */
    public function __get($name)
    {
        if ($this->hasComponent($name)) {
            return $this->getComponent($name);
        }
        return $this->$name; //notice if property doesn't exist
    }

    /**
     * Checks if a component with the given name exists
     * @param string $name
     * @return bool
     */
    function __isset($name)
    {
        return $this->hasComponent($name);
    }

    /**
     * Removes the specified component
     * @param string $name
     */
    function __unset($name)
    {
        if ($this->hasComponent($name))
            $this->removeComponent($name);
    }

    /**
     * Sets child component
     * @param string $name
     * @param mixed $object
     */
    public function __set($name, $object)
    {
        if ($object instanceof ComponentInterface) {
            $this->addComponent($object, $name);
        } else {
            $this->$name = $object;
        }
    }

    /* (non-PHPdoc)
     * @see Vivo\UI.ComponentContainerInterface::addComponent()
     */
    public function addComponent(ComponentInterface $component, $name)
    {
        //TODO check cycles in component tree
        $component->setParent($this, $name);
        $this->components[$name] = $component;
    }

    public function addComponents(array $components)
    {
        foreach ($components as $name => $component) {
            $this->addComponent($component, $name);
        }
    }

    /* (non-PHPdoc)
     * @see Vivo\UI.ComponentContainerInterface::removeComponent()
     */
    public function removeComponent($name)
    {
        //TODO also accept component object as parameter

        if (!$this->hasComponent($name)) {
            throw new ComponentNotExists(
                "Component `$name` doesn't exist in container `"
                    . $this->getPath() . "`.");
        }
        $this->getComponent($name)->setParent(null, null);
        unset($this->components[$name]);
    }

    public function removeAllComponents()
    {
        foreach (array_keys($this->components) as $name) {
            $this->removeComponent($name);
        }
    }

    /**
     * Returns component by name
     * @param string $name
     * @return ComponentInterface
     */
    public function getComponent($name)
    {
        if (!$this->hasComponent($name)) {
            throw new ComponentNotExists(
                "Component `$name` doesn't exist in container `"
                    . $this->getPath() . "`.");
        }
        return $this->components[$name];
    }

    /**
     * Returns components.
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasComponent($name)
    {
        return isset($this->components[$name]);
    }

    /**
     * Listener for ComponentEventInterface::EVENT_VIEW event
     * @param ComponentEventInterface $event
     * @throws Exception\RuntimeException
     */
    public function viewListenerChildViews(ComponentEventInterface $event)
    {
        if ($event->getComponent() != $this) {
            //This view listener expects only this component as target
            throw new Exception\RuntimeException(sprintf("%s: Unexpected component", __METHOD__));
        }
        $viewModel = $this->getView();
        foreach ($this->components as $name => $component) {
            $model = $component->getView();
            if ($model instanceof ModelInterface) {
                $viewModel->addChild($model, $name);
            } else {
                $viewModel->setVariable($name, $model);
            }
        }
        $container = array (
            'components' => array_keys($this->components),
        );
        $viewModel->setVariable('container', $container);
    }

    /**
     * Attaches listeners
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function attachListeners(ServiceLocatorInterface $serviceLocator)
    {
        parent::attachListeners($serviceLocator);
        $eventManager   = $this->getEventManager();
        //View
        $eventManager->attach(ComponentEventInterface::EVENT_VIEW, array($this, 'viewListenerChildViews'));
    }
}
