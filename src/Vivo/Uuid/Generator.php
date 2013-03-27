<?php
namespace Vivo\Uuid;

/**
 * Generator
 * UUID Generator
 */
class Generator implements GeneratorInterface
{
    /**
     * Creates and returns a UUID
     * @return string
     */
    public function create()
    {
        $uuid   = strtoupper(md5(uniqid()));
        return $uuid;
    }
}