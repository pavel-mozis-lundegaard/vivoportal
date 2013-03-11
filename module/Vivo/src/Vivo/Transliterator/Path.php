<?php
namespace Vivo\Transliterator;

use Zend\Stdlib\ArrayUtils;

/**
 * Path
 * Path transliterator. Transliterates strings to be usable as file system paths.
 */
class Path implements TransliteratorInterface
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
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'JO', 'Ж' => 'ZH',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
            'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'C', 'Ч' => 'CH',
            'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'EH', 'Ю' => 'JU', 'Я' => 'JA',
            //Doubles
            'ß' => 'ss', 'æ' => 'ae', 'Æ' => 'AE', 'œ' => 'oe', 'Œ' => 'OE',
            //A
            'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'ą' => 'a', 'à' => 'a', 'À' => 'A', 'â' => 'a', 'Â' => 'A',
            'å' => 'a', 'Å' => 'A', 'ă' => 'a', 'Ă' => 'A',
            //C
            'č' => 'c', 'Č' => 'C', 'ć' => 'c', 'Ć' => 'C', 'ç' => 'c', 'Ç' => 'C',
            //D
            'ď' => 'd', 'Ď' => 'D', 'ð' => 'd', 'Ð' => 'D',
            //E
            'é' => 'e', 'É' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ę' => 'e', 'Ę' => 'E',
            'è' => 'e', 'È' => 'E', 'ê' => 'e', 'Ê' => 'E',
            //I
            'í' => 'i', 'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'î' => 'i', 'Î' => 'I',
            //L
            'ľ' => 'l', 'Ľ' => 'L', 'ĺ' => 'l', 'Ĺ' => 'L', 'ł' => 'l', '£' => 'L',
            //N
            'ň' => 'n', 'Ň' => 'N', 'ń' => 'n', 'Ń' => 'N', 'ñ' => 'n', 'Ñ' => 'N',
            //O
            'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O',
            //R
            'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R',
            //S
            'š' => 's', 'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ş' => 's', 'Ş' => 'S',
            //T
            'ť' => 't', 'Ť' => 'T', 'ţ' => 't', 'Ţ' => 'T',
            //U
            'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ű' => 'u', 'Ű' => 'U',
            'û' => 'u', 'Û' => 'U', 'ù' => 'u',
            //Y
            'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
            //Z
            'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z', 'ż' => 'z', 'Ż' => 'Z',
            //Symbols
            '\\' => '/',
        ),
        //String with all allowed characters
        'allowedChars'      => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_/.',
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
        $translit   = mb_eregi_replace($re, $this->options['replacementChar'], $translit);
        //Remove leading replacement char
        $re         = sprintf('^\\%s', $this->options['replacementChar']);
        $translit   = mb_eregi_replace($re, '', $translit);
        //Remove trailing replacement char
        $re         = sprintf('\\%s$', $this->options['replacementChar']);
        $translit   = mb_eregi_replace($re, '', $translit);
        return $translit;
    }
}
