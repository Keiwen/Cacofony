<?php

namespace Keiwen\Cacofony\Twig;


use Symfony\Component\Intl\Intl;

class TwigIntl extends \Twig_Extension
{


    /**
     * @return string
     */
    public function getName()
    {
        return 'caco_twig_intl_extension';
    }


    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ountryName', array($this, 'countryNameFilter')),
            new \Twig_SimpleFilter('languageName', array($this, 'languageNameFilter')),
            new \Twig_SimpleFilter('intlScriptName', array($this, 'scriptNameFilter')),
            new \Twig_SimpleFilter('intlLocaleName', array($this, 'localeNameFilter')),
            new \Twig_SimpleFilter('currencyName', array($this, 'currencyNameFilter')),
            new \Twig_SimpleFilter('currencySymbol', array($this, 'currencySymbolFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('countryNames', array($this, 'countryNames')),
        );
    }


    /**
     * @param string $countryCode
     * @return string
     */
    public function countryNameFilter(string $countryCode)
    {
        return Intl::getRegionBundle()->getCountryName(strtoupper($countryCode));
    }

    /**
     * @param $format
     * @return string|string[]
     */
    public function countryNames($format)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();
        if($format == 'json') return json_encode($countries);
        return $countries;
    }


    /**
     * @param string $languageCode
     * @param string $region
     * @return string
     */
    public function languageNameFilter(string $languageCode, string $region = '')
    {
        if(strpos($languageCode, '_') !== false) {
            list($languageCode, $languageRegion) = explode('_', $languageCode);
            if(empty($region)) $region = $languageRegion;
        }
        return Intl::getLanguageBundle()->getLanguageName(strtolower($languageCode), strtoupper($region));
    }


    /**
     * @param string $scriptCode
     * @return string
     */
    public function scriptNameFilter(string $scriptCode)
    {
        return Intl::getLanguageBundle()->getScriptName(ucfirst(strtolower($languageCode)));
    }


    /**
     * @param string $localeCode
     * @return string
     */
    public function localeNameFilter(string $localeCode)
    {
        return Intl::getLocaleBundle()->getLocaleName($localeCode);
    }

    /**
     * @param string $currencyCode
     * @return string
     */
    public function currencyNameFilter(string $currencyCode)
    {
        return Intl::getCurrencyBundle()->getCurrencyName(strtoupper($currencyCode));
    }

    /**
     * @param string $currencyCode
     * @return string
     */
    public function currencySymbolFilter(string $currencyCode)
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol(strtoupper($currencyCode));
    }


}
