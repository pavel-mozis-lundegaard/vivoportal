<?php
namespace Vivo\Transliterator;

use Zend\Stdlib\ArrayUtils;

/**
 * Url
 * Url transliterator. Transliterates strings to be usable as URLs
 */
class Url implements TransliteratorInterface
{
    /**
     * Transliterator options
     * @var array
     */
    protected $options  = array(
        //Transliteration map
        'map'               => array(
            //Cyrillic
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
            'з' => 'z', 'и' =>'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'ju', 'я' => 'ja',
            //Doubles
            'ß' => 'ss', 'æ' => 'ae', 'œ' => 'oe',
            //A
            'á' => 'a', 'ä' => 'a', 'ą' => 'a', 'à' => 'a', 'â' => 'a', 'å' => 'a', 'ă' => 'a',
            //C
            'č' => 'c', 'ć' => 'c', 'ç' => 'c',
            //D
            'ď' => 'd', 'ð' => 'd',
            //E
            'é' => 'e', 'ě' => 'e', 'ë' => 'e', 'ę' => 'e', 'è' => 'e', 'ê' => 'e',
            //I
            'í' => 'i', 'ï' => 'i', 'î' => 'i',
            //L
            'ľ' => 'l', 'ĺ' => 'l', 'ł' => 'l',
            //N
            'ň' => 'n', 'ń' => 'n', 'ñ' => 'n',
            //O
            'ó' => 'o', 'ö' => 'o', 'ô' => 'o', 'ő' => 'o',
            //R
            'ř' => 'r', 'ŕ' => 'r',
            //S
            'š' => 's', 'ś' => 's', 'ş' => 's',
            //T
            'ť' => 't', 'ţ' => 't',
            //U
            'ú' => 'u', 'ů' => 'u', 'ü' => 'u', 'ű' => 'u', 'û' => 'u', 'ù' => 'u',
            //Y
            'ý' => 'y', 'ÿ' => 'y',
            //Z
            'ž' => 'z', 'ź' => 'z', 'ż' => 'z',
        ),
        //String with all allowed characters
        'allowedChars'      => 'abcdefghijklmnopqrstuvwxyz-/',
        //Character used to replace illegal characters
        'replacementChar'   => '-',
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
        $str    = mb_strtolower($str);
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
        return $translit;
    }
}
