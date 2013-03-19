<?php
namespace Vivo\CMS\Model;

/**
 * SymRefDataExchangeInterface
 * Models implementing this interface provide/accept their data for symbolic reference conversion
 * This interface is analogous to the Zend\StdLib\ArraySerializableInterface, except methods from this interface
 * work with only a subset of the object properties (only the properties which may contain URLs / symbolic refs)
 * @package Vivo\CMS\Model
 */
interface SymRefDataExchangeInterface
{
    /**
     * Exchange internal values containing symbolic refs / URLs from provided array
     * @param  array $data
     * @return void
     */
    public function exchangeArraySymRef(array $data);

    /**
     * Return an array representation of the object's properties containing symbolic refs / URLs
     * @return array
     */
    public function getArrayCopySymRef();
}
