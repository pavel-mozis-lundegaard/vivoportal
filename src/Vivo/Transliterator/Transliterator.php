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
        foreach ($this->options['map'] as $from => $to) {
            $re     = sprintf('\\%s', $from);
            $str    = mb_ereg_replace($re, $to, $str);
        }
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
        $re         = sprintf('^\\%s', $this->options['replacementChar']);
        $translit   = mb_ereg_replace($re, '', $translit);
        //Remove trailing replacement char
        $re         = sprintf('\\%s$', $this->options['replacementChar']);
        $translit   = mb_ereg_replace($re, '', $translit);
        //Change case POST
        switch ($this->options['caseChangePost']) {
            case self::CASE_CHANGE_TO_LOWER:
                $translit   = mb_strtolower($translit);
                break;
            case self::CASE_CHANGE_TO_UPPER:
                $translit   = mb_strtoupper($translit);
                break;
        }
        return $translit;
    }
}
