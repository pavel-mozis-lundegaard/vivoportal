<?php
namespace Vivo\Transliterator;

/**
 * TransliteratorInterface
 */
interface TransliteratorInterface
{
    /**
     * Transliterates string
     * @param string $str
     * @return string
     */
    public function transliterate($str);
}
