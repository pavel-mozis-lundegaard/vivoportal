<?php
namespace Vivo\CMS\RefInt;

use Vivo\CMS\Model\SymRefDataExchangeInterface;

/**
 * Interface SymRefConvertorInterface
 * @package Vivo\CMS\RefInt
 */
interface SymRefConvertorInterface
{
    /**
     * Pattern describing URL
     */
    const PATTERN_URL   = '\/[\w\d\-\/\.]+\/';

    /**
     * Pattern describing UUID
     */
    const PATTERN_UUID  = '[\d\w]{32}';

    /**
     * Converts URLs to symbolic references
     * @param string|array|SymRefDataExchangeInterface $value
     * @return string|array|SymRefDataExchangeInterface The same object / value
     */
    function convertUrlsToReferences($value);

    /**
     * Converts symbolic references to URLs
     * @param string|array|SymRefDataExchangeInterface $value
     * @return string|array|SymRefDataExchangeInterface $value The same object / value
     */
    function convertReferencesToURLs($value);
}
