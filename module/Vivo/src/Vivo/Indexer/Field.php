<?php
namespace Vivo\Indexer;

/**
 * Field
 * Represents a document field
 */
class Field
{
    /**
     * Field name
     * @var string
     */
    protected $name;

    /**
     * Field value
     * @var string
     */
    protected $value;

    /**
     * Field is to be stored in the index for return with search hits.
     * @var boolean
     */
    protected $isStored;

    /**
     * Field is to be indexed, so that it may be searched on.
     * @var boolean
     */
    protected $isIndexed;

    /**
     * Field should be tokenized as text prior to indexing.
     * @var boolean
     */
    protected $isTokenized;

    /**
     * Field is stored as binary.
     * @var boolean
     */
    protected $isBinary;

    /**
     * Constructor
     * @param string $name
     * @param string $value
     * @param bool $isStored
     * @param bool $isIndexed
     * @param bool $isTokenized
     * @param bool $isBinary
     */
    public function __construct($name, $value,
                                $isStored = false, $isIndexed = false, $isTokenized = false, $isBinary = false)
    {
        $this->name  = $name;
        $this->value = $value;
        if ($isBinary) {
            $this->isTokenized  = false;
            $this->isStored     = true;
            $this->isIndexed    = false;
        } else {
            $this->isTokenized  = $isTokenized;
            $this->isStored     = $isStored;
            $this->isIndexed    = $isIndexed;
        }
        $this->isBinary  = $isBinary;
    }

    /**
     * Is the field binary?
     * @return boolean
     */
    public function isBinary()
    {
        return $this->isBinary;
    }

    /**
     * Is the field indexed?
     * @return boolean
     */
    public function isIndexed()
    {
        return $this->isIndexed;
    }

    /**
     * Is the field stored?
     * @return boolean
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * Is the field tokenized?
     * @return boolean
     */
    public function isTokenized()
    {
        return $this->isTokenized;
    }

    /**
     * Returns the name of the field
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return value of the field
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
