<?php
namespace Vivo\UI;

use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;

/**
 * Base class for UI components.
 *
 */
class Component implements ComponentInterface
{

    /**
     * String used to separate component names in path
     * @var string
     */
    const COMPONENT_SEPARATOR = '->';

    /**
     * Component parent.
     * @var \Vivo\UI\ComponentInterface
     */
    private $parent;

    /**
     * Component name.
     * @var string
     */
    private $name;

    /**
     *
     * @var ModelInterface
     */
    protected $view;

    /**
     * @param ModelInterface $view
     */
    public function setView(ModelInterface $view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        if ($this->view === null) {
            $this->view = new ViewModel();
        }
        return $this->view;
    }

    public function init()
    {

    }

    public function view()
    {
        if ($this->getView()->getTemplate() == '') {
            $this->getView()->setTemplate($this->getDefaultTemplate());
        }
        $component = array(
                'component' => $this,
                'path' => $this->getPath(),
                'name' => $this->getName(),
        );
        $this->getView()->setVariable('component', $component);
        return $this->view;
    }

    public function done()
    {

    }

    public function getPath($path = '')
    {
        $component = $this;
        while ($component) {
            $name = $component->getName();
            $path = $path ? ($name ? $name . self::COMPONENT_SEPARATOR : $name)
                    . $path : $name;
            $component = $component->getParent();
        }
        return $path;
    }

    public function getDefaultTemplate()
    {
        return get_class($this);
    }

    /**
     * Return parent of component in component tree.
     * @return ComponentContainerInterface
     */
    public function getParent($className = null)
    {
        $parent = $this->parent;
        if ($className) {
            while ($parent && !$parent instanceof $className) {
                $parent = $parent->getParent();
            }
        }
        return $parent;
    }

    /**
     * Sets parent component.
     * This method should be called only from ComponentContainer::addComponent()
     * @param ComponentContainerInterface $parent
     * @param string $name
     */
    public function setParent(ComponentContainerInterface $parent = null, $name)
    {
        $this->parent = $parent;
        if ($name) {
            $this->setName($name);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        //TODO check name format (alfanum)
        $this->name = $name;
    }
}
