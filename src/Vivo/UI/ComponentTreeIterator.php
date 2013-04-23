<?php
namespace Vivo\UI;

/**
 * Iterator for iterating component tree or subtree.
 *
 */
class ComponentTreeIterator implements \RecursiveIterator
{
    /**
     * @var array
     */
    protected $components = array();

    /**
     * Constructor.
     * @param $components
     */
    public function __construct($components)
    {
        if (is_array($components))
            $this->components = $components;
        else
            $this->components = array($components);
    }

    /**
     * Whether current component has children.
     * @return boolean
     */
    public function hasChildren()
    {
        return (current($this->components) instanceof ComponentContainerInterface);
    }

    /**
     * Returns iterator for current component.
     * @return \Vivo\UI\ComponentTreeIterator
     */
    public function getChildren()
    {
        return new self(current($this->components)->getComponents());
    }

    /**
     * Returns current component.
     * @return ComponentInterface
     */
    public function current()
    {
        return current($this->components);
    }

    /**
     * Returns current component key (name).
     */
    public function key()
    {
        return key($this->components);
    }

    /**
     * Returns next component.
     * @return ComponentInterface|false
     */
    public function next()
    {
        return next($this->components);
    }

    /**
     * Rewind itarator.
     */
    public function rewind()
    {
        return reset($this->components);
    }


    /**
     * Whether current value is valid.
     * @return boolean
     */
    public function valid()
    {
        return current($this->components) === false ? false : true;
    }
}
