<?php
namespace Vivo\UI;

use Zend\View\Model\ModelInterface;

/**
 * Base class for UI components.
 *
 */
class Component implements ComponentInterface
{

    /**
     * String used to separate names of component in path.
     * @var string
     */
    const COMPONENT_SEPARATOR = '->';

    /**
     * Template name
     * @var string
     */
    private $template;

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

    public function __construct() {

    }

    /**
     * @param ModelInterface $view
     */
    public function setView(ModelInterface $view)
    {
        $this->view = $view;
    }

    public function init()
    {

    }

    public function view()
    {
        $this->view->setTemplate($this->getTemplate());
        $this->view->setVariable('component', $this);
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

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template ? : get_class($this);
    }

    /**
     * Return parent of component in component tree.
     * @return ComponentContainerInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets parent component.
     * This method should be called only from ComponentContainer::addComponent()
     * @param ComponentContainerInterface $parent
     * @param string $name
     */
    public function setParent(ComponentContainerInterface $parent, $name)
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
