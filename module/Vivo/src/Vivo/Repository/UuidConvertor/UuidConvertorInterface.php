<?php
namespace Vivo\Repository\UuidConvertor;

/**
 * UuidConvertorInterface
 * Converts to/from UUID
 */
interface UuidConvertorInterface
{
    /**
     * Returns entity UUID based on its path
     * If the entity does not exist returns null
     * @param string $path
     * @return null|string
     */
    public function getUuidFromPath($path);

    /**
     * Returns entity path based on its UUID
     * If the UUID does not exist returns null
     * @param string $uuid
     * @return null|string
     */
    public function getPathFromUuid($uuid);

}