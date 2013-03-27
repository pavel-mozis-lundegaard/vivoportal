<?php
namespace Vivo\Metadata;

/**
 * MetadataValueProviderInterface
 */
interface MetadataValueProviderInterface
{
    /**
     * Returns data to be used as metadata value
     * @param string $entityClass
     * @return mixed
     */
    public function getValue($entityClass);
}
