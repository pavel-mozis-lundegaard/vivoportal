<?php
namespace Vivo\Storage;

use Vivo\Storage\PathBuilder\PathBuilderInterface;

/**
 * AbstractStorage
 * Common storage functionality
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Path builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Sets the Path builder
     * @param PathBuilderInterface $pathBuilder
     */
    public function setPathBuilder(PathBuilderInterface $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Returns PathBuilder for this storage
     * @return PathBuilderInterface
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }
}