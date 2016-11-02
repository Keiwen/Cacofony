<?php

namespace Keiwen\Cacofony\Twig;


class TwigExtension extends \Twig_Extension
{

    /**
     * @return string
     */
	public function getName()
    {
		return 'caco_twig_extension';
	}

    /**
     * @return array
     */
	public function getFilters()
    {
		return array(
            new \Twig_SimpleFilter('numberFormat', array($this, 'numberFormatFilter'), array('is_safe' => array('html'))),
			new \Twig_SimpleFilter('price', array($this, 'priceFilter'), array('is_safe' => array('html'))),
		);
	}

    /**
     * @return array
     */
	public function getFunctions()
    {
		return array(
			'file_exists' => new \Twig_SimpleFunction('file_exists', 'file_exists'),
		);
	}

    /**
     * @param int|string $formattedNumber
     * @param string $currency
     * @param bool $currencyFirst
     * @return string
     */
	public function priceFilter($formattedNumber,
                                string $currency = '€',
                                bool $currencyFirst = false)
    {
		if($currencyFirst) {
			$price = $currency . '&nbsp;' . $formattedNumber;
		} else {
			$price = $formattedNumber . '&nbsp;' . $currency;
		}
		return $price;
	}

    /**
     * @param int $number
     * @param int $decimals
     * @param string $decSep
     * @param string $thousandSep
     * @return string
     */
    public function numberFormatFilter(int $number,
                                       int $decimals = 0,
                                       string $decSep = ',',
                                       string $thousandSep = '&nbsp;')
    {
        $formatted = number_format($number, $decimals, $decSep, $thousandSep);
        return $formatted;
    }


}