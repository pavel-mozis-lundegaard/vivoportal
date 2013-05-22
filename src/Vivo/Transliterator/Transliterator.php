<?php
namespace Vivo\Transliterator;

use Zend\Stdlib\ArrayUtils;

/**
 * Transliterator
 * General transliterator implementation
 */
class Transliterator implements TransliteratorInterface
{
    /**#@+
     * Constant signalling required case change
     */
    const CASE_CHANGE_TO_LOWER  = -1;
    const CASE_CHANGE_TO_UPPER  = 1;
    const CASE_CHANGE_NONE      = 0;
    /**#@-*/

    /**
     * Cached transliterator results
     * @var array
     */
    protected $cache    = array();

    /**
     * Transliterator options
     * @var array
     */
    protected $options  = array(
        //Transliteration map
        'map'               => array(),
        //String with all allowed characters
        'allowedChars'      => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_',
        //Character used to replace illegal characters
        'replacementChar'   => '-',
        //Change case before processing
        'caseChangePre'     => self::CASE_CHANGE_NONE,
        //Change case after processing
        'caseChangePost'    => self::CASE_CHANGE_NONE,
    );

    /**
     * Constructor
     * @param array $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $options = array())
    {
        $this->options  = ArrayUtils::merge($this->options, $options);
        if (mb_strpos($this->options['allowedChars'], $this->options['replacementChar']) === false) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Replacement character '%s' missing in allowed characters '%s'",
                        __METHOD__, $this->options['replacementChar'], $this->options['allowedChars']));
        }
    }

    /**
     * Transliterates string
     * @param string $str
     * @return string
     */
    public function transliterate($str)
    {
        $orig   = $str;
        if (array_key_exists($str, $this->cache)) {
            return $this->cache[$str];
        }


        //Change case PRE
        switch ($this->options['caseChangePre']) {
            case self::CASE_CHANGE_TO_LOWER:
                $str    = mb_strtolower($str);
                break;
            case self::CASE_CHANGE_TO_UPPER:
                $str    = mb_strtoupper($str);
                break;
        }

        //Replace according to the map
//        foreach ($this->options['map'] as $from => $to) {
//            $re     = sprintf('\\%s', $from);
//            $str    = mb_ereg_replace($re, $to, $str);
//        }
        foreach ($this->options['map'] as $from => $to) {
            $str    = strtr($str, array($from => $to));
        }

        $replacementCharLength  = mb_strlen($this->options['replacementChar']);

        //Replace illegal chars with the replacement char
        $translit   = '';
        $len        = mb_strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $chr        = mb_substr($str, $i, 1);
            $translit   .= (mb_strpos($this->options['allowedChars'], $chr) !== false)
                            ? $chr : $this->options['replacementChar'];
        }
        //Remove duplicated replacement chars
        $re         = sprintf('\\%s+', $this->options['replacementChar']);
        $translit   = mb_ereg_replace($re, $this->options['replacementChar'], $translit);


        //Remove leading replacement char
//        $re         = sprintf('^\\%s', $this->options['replacementChar']);
//        $translit   = mb_ereg_replace($re, '', $translit);
//        if (mb_strpos($translit, $this->options['replacementChar']) === 0) {
//            $translit   = mb_substr($translit, $replacementCharLength);
//        }
        if (mb_substr($translit, 0, $replacementCharLength) == $this->options['replacementChar']) {
            $translit   = mb_substr($translit, $replacementCharLength);
        }


        //Remove trailing replacement char
//        $re         = sprintf('\\%s$', $this->options['replacementChar']);
//        $translit   = mb_ereg_replace($re, '', $translit);
        $translitLength = mb_strlen($translit);
//        if (mb_strrpos($translit, $this->options['replacementChar']) === $translitLength - $replacementCharLength) {
//            $translit   = mb_substr($translit, -$replacementCharLength);
//        }
        if (mb_substr($translit, $translitLength - $replacementCharLength) == $this->options['replacementChar']) {
            $translit   = mb_substr($translit, -$replacementCharLength);
        }

        //Change case POST
        switch ($this->options['caseChangePost']) {
            case self::CASE_CHANGE_TO_LOWER:
                $translit   = mb_strtolower($translit);
                break;
            case self::CASE_CHANGE_TO_UPPER:
                $translit   = mb_strtoupper($translit);
                break;
        }

        $this->cache[$orig] = $translit;

        return $translit;
    }
}
