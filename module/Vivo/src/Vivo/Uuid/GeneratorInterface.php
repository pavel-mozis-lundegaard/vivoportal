<?php
namespace Vivo\Uuid;

/**
 * GeneratorInterface
 */
interface GeneratorInterface
{
    /**
     * Creates and returns a UUID
     * @return string
     */
    public function create();
}