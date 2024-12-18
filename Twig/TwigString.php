<?php

namespace Keiwen\Cacofony\Twig;

use Keiwen\Utils\Sanitize\StringSanitizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigString extends AbstractExtension
{

    /** @var StringSanitizer $stringSanitizer */
    protected $stringSanitizer;

    /**
     * TwigString constructor.
     * @param StringSanitizer|null $stringSanitizer
     */
    public function __construct(?StringSanitizer $stringSanitizer = null)
    {
        if($stringSanitizer === null) $stringSanitizer = new StringSanitizer();
        $this->stringSanitizer = $stringSanitizer;
    }


    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('ucfirst', 'ucfirst'),
            new TwigFilter('lcfirst', 'lcfirst'),
            new TwigFilter('ucwords', 'ucwords'),
            new TwigFilter('str_repeat', 'str_repeat'),
            new TwigFilter('str_word_count', 'str_word_count'),
            new TwigFilter('base64_encode', 'base64_encode'),
            new TwigFilter('base64_decode', 'base64_decode'),

            new TwigFilter('str_limit', array($this, 'strLimitFilter')),
            new TwigFilter('escchar', array($this, 'escCharFilter')),
            new TwigFilter('escquote', array($this, 'escQuoteFilter')),
            new TwigFilter('string_sanitize', array($this->stringSanitizer, 'get')),
        );
    }


    /**
     * When input string is too long, reduce to max size allowed and add complement
     * @param string $string
     * @param int    $limitLength max length of resulting string
     * @param string $complement string addition added at the end ot offsized string
     * @return string
     */
    public function strLimitFilter(string $string, int $limitLength, string $complement = '...')
    {
        if(strlen($string) <= $limitLength) return $string;
        $complementLength = strlen($complement);
        $limitLength -= $complementLength;
        if($limitLength <= 0) return $string;
        $string = substr($string, 0, $limitLength);
        return $string . $complement;
    }

    /**
     * escape given characters
     * @param string $string
     * @return string
     */
    public function escCharFilter(string $string, string $charList)
    {
        $charList = str_split($charList);
        foreach($charList as $char) {
            $string = str_replace($char, "\\$char", $string);
        }
        return $string;
    }

    /**
     * escape quote characters
     * @param string $string
     * @return string
     */
    public function escQuoteFilter(string $string)
    {
        return $this->escCharFilter($string, "'");
    }

}
