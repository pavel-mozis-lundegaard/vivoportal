<?php
namespace Vivo\Util;

/**
 * Tree data structure implementation.
 */
class DataTree implements \RecursiveIterator
{

    /**
     * @var DataTree
     */
    protected $parent;

    /**
     * Node value.
     * @var mixed
     */
    public $value;

    /**
     * Node children
     * @var Tree[]
     */
    protected $children = array();

    /**
     * Constructor.
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * (non-PHPdoc)
     * @see RecursiveIterator::hasChildren()
     */
    public function hasChildren()
    {
        return current($this->children);
    }

    /**
     * Returns current child, for all children use self::getChildrenNodes.
     * @see RecursiveIterator::getChildren()
     */
    public function getChildren()
    {
        return current($this->children);
    }
    /**
     *
     * @return \Vivo\Util\Tree[]
     */
    public function getChildrenNodes()
    {
        return $this->children;
    }

    /**
     * Adds value as new child.
     * @param mixed $value
     */
    public function addChildValue($value)
    {
        $this->addChild(new self($value));
    }

    /**
     * Get node value.
     * @return mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Sets node value.
     * @param mixed $value
     */
    public function setValue($value){
        $this->value = $value;
    }

    /**
     * Adds child node.
     * @param self $child
     */
    public function addChild(self $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    /**
     * Sets node parent.
     * @param DataTree $parent
     */
    public function setParent(DataTree $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \Vivo\Util\DataTree
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return how deep is node in tree.
     *
     * Number of parents (deep of root node is 0).
     * @return integer
     */
    public function getDeep()
    {
        $deep = 0;
        $parent = $this;
        while($parent = $parent->getParent())$deep++;
        return $deep;
    }

    /**
     * Returns curent component.
     * @return ComponentInterface
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * (non-PHPdoc)
     * @see RecursiveIterator::key()
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * (non-PHPdoc)
     * @see RecursiveIterator::next()
     */
    public function next()
    {
        return next($this->children);
    }

    /**
     * (non-PHPdoc)
     * @see RecursiveIterator::rewind()
     */
    public function rewind()
    {
        return reset($this->children);
    }

    /**
     * (non-PHPdoc)
     * @see RecursiveIterator::valid()
     */
    public function valid()
    {
        return current($this->children) === false ? false : true;
    }
}
