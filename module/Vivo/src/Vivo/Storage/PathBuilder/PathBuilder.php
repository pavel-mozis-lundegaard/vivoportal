<?php
namespace Vivo\Storage\PathBuilder;

/**
 * PathBuilder
 * Storage paths manipulation
 */
class PathBuilder implements PathBuilderInterface
{
    /**
     * Character used as a separator for paths in storage
     * @var string
     */
    protected $separator   = '/';

    /**
     * Constructor
     * @param string $separator Path components separator
     */
    public function __construct($separator)
    {
        $this->separator    = $separator;
    }

    /**
     * Returns character used as a separator in storage paths
     * @return string
     */
    public function getStoragePathSeparator()
    {
        return $this->separator;
    }

    /**
     * Builds storage path from submitted elements
     * @param array $elements
     * @param bool $absolute If true, builds an absolute path starting with the storage path separator
     * @return string
     */
    public function buildStoragePath(array $elements, $absolute = false)
    {
        $components = array();
        //Get atomic components
        foreach ($elements as $key => $element) {
            $elementComponents  = $this->getStoragePathComponents($element);
            $components         = array_merge($components, $elementComponents);
        }
        $path   = implode($this->getStoragePathSeparator(), $components);
        if ($absolute) {
            $path    = $this->getStoragePathSeparator() . $path;
        }
        return $path;
    }

    /**
     * Returns an array of 'atomic' storage path components
     * @param string $path
     * @return array
     */
    public function getStoragePathComponents($path)
    {
        $components = explode($this->getStoragePathSeparator(), $path);
        foreach ($components as $key => $value) {
            if (empty($value)) {
                unset($components[$key]);
            }
        }
        //Reset array indices
        $components = array_values($components);
        return $components;
    }
}