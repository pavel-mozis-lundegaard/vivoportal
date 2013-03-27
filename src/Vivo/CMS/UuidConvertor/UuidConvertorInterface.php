<?php
namespace Vivo\CMS\UuidConvertor;

/**
 * UuidConvertorInterface
 * Converts to/from UUID/path
 */
interface UuidConvertorInterface
{
    /**
     * Returns entity UUID based on its path
     * If the entity does not exist returns null
     * @param string $path
     * @return null|string
     */
    public function getUuid($path);

    /**
     * Returns entity path based on its UUID
     * If the UUID does not exist returns null
     * @param string $uuid
     * @return null|string
     */
    public function getPath($uuid);

    /**
     * Sets a conversion result (uuid and its associated path) into the result cache
     * Overwrites previously cached results
     * @param string $uuid
     * @param string $path
     */
    public function set($uuid, $path);

    /**
     * Removes a conversion result (uuid and its associated path) from the result cache
     * If $uuid is not found in cached results, does nothing
     * @param string $uuid
     */
    public function removeByUuid($uuid);

    /**
     * Removes a conversion result (path and its associated uuid) from the result cache
     * If $path is not found in cached results, does nothing
     * @param string $path
     */
    public function removeByPath($path);
}