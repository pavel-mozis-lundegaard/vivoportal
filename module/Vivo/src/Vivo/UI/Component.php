<?php
namespace Vivo\UI;

use Vivo\View\Model\UIViewModel;

use Zend\View\Model\ModelInterface;

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

    public function __construct()
    {

    }

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
            $this->view = new UIViewModel();
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
        $this->getView()->setVariable('component', $this);
        $this->getView()->setVariable('cpath', $this->getPath());
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

    public function getDefaultTemplate()
    {
        return get_class($this);
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
