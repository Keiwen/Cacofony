<?php

namespace Keiwen\Cacofony\Twig;


class TwigString extends \Twig_Extension
{

    /**
     * @return string
     */
	public function getName()
    {
		return 'caco_twig_string_extension';
	}

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ucfirst', 'ucfirst'),
            new \Twig_SimpleFilter('lcfirst', 'lcfirst'),
            new \Twig_SimpleFilter('ucwords', 'ucwords'),
            new \Twig_SimpleFilter('str_repeat', 'str_repeat'),
            new \Twig_SimpleFilter('str_word_count', 'str_word_count'),

            new \Twig_SimpleFilter('str_limit', array($this, 'strLimitFilter')),
            new \Twig_SimpleFilter('escchar', array($this, 'escCharFilter')),
            new \Twig_SimpleFilter('escquote', array($this, 'escQuoteFilter')),
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
        if(strlen($string <= $limitLength)) return $string;
        $complementLength = strlen($complement);
        $limitLength -= $complementLength;
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
