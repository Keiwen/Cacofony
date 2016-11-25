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

}
