<?php

namespace Keiwen\Cacofony\Twig;


use Symfony\Component\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigIntl extends AbstractExtension
{

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('countryName', array($this, 'countryNameFilter')),
            new TwigFilter('languageName', array($this, 'languageNameFilter')),
            new TwigFilter('intlScriptName', array($this, 'scriptNameFilter')),
            new TwigFilter('intlLocaleName', array($this, 'localeNameFilter')),
            new TwigFilter('currencyName', array($this, 'currencyNameFilter')),
            new TwigFilter('currencySymbol', array($this, 'currencySymbolFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('countryNames', array($this, 'countryNames')),
            new TwigFunction('languageNames', array($this, 'languageNames')),
            new TwigFunction('intlScriptNames', array($this, 'scriptNames')),
            new TwigFunction('intlLocaleNames', array($this, 'localeNames')),
            new TwigFunction('currencyNames', array($this, 'currencyNames')),
        );
    }

    /**
     * @param string $format
     * @param string $displayLocale
     * @return string|string[]
     */
    public function countryNames(string $format = 'array', ?string $displayLocale = null)
    {
        $countries = Intl\Countries::getNames($displayLocale);
        if($format == 'json') return json_encode($countries);
        return $countries;
    }

    /**
     * @param string $countryCode
     * @param string $displayLocale
     * @return string
     */
    public function countryNameFilter(string $countryCode, ?string $displayLocale = null)
    {
        $name = '';
        try {
            $name = Intl\Countries::getName(strtoupper($countryCode), $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $name;
    }


    /**
     * @param string $format
     * @param string $displayLocale
     * @return string|string[]
     */
    public function currencyNames(string $format = 'array', ?string $displayLocale = null)
    {
        $currencies = Intl\Currencies::getNames($displayLocale);
        if($format == 'json') return json_encode($currencies);
        return $currencies;
    }

    /**
     * @param string $currencyCode
     * @param string $displayLocale
     * @return string
     */
    public function currencyNameFilter(string $currencyCode, ?string $displayLocale = null)
    {
        $name = '';
        try {
            $name = Intl\Currencies::getName(strtoupper($currencyCode), $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $name;
    }

    /**
     * @param string $currencyCode
     * @param string $displayLocale
     * @return string
     */
    public function currencySymbolFilter(string $currencyCode, ?string $displayLocale = null)
    {
        $symbol = '';
        try {
            $symbol = Intl\Currencies::getSymbol(strtoupper($currencyCode), $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $symbol;
    }


    /**
     * @param string $format
     * @param string $displayLocale
     * @return string|string[]
     */
    public function languageNames(string $format = 'array', ?string $displayLocale = null)
    {
        $languages = Intl\Languages::getNames($displayLocale);
        if($format == 'json') return json_encode($languages);
        return $languages;
    }


    /**
     * @param string $languageCode
     * @param string $displayLocale
     * @return string
     */
    public function languageNameFilter(string $languageCode, ?string $displayLocale = null)
    {
        $languageCode = strtolower($languageCode);
        if(strpos($languageCode, '_') !== false) {
            list($languageCode, $languageRegion) = explode('_', $languageCode, 2);
            $languageCode .= '_' . strtoupper($languageRegion);
        }
        $name = '';
        try {
            $name = Intl\Languages::getName($languageCode, $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $name;
    }

    /**
     * @param string $format
     * @param string $displayLocale
     * @return string|string[]
     */
    public function scriptNames(string $format = 'array', ?string $displayLocale = null)
    {
        $scripts = Intl\Scripts::getNames($displayLocale);
        if($format == 'json') return json_encode($scripts);
        return $scripts;
    }


    /**
     * @param string $scriptCode
     * @param string $displayLocale
     * @return string
     */
    public function scriptNameFilter(string $scriptCode, ?string $displayLocale = null)
    {
        $name = '';
        try {
            $name = Intl\Scripts::getName(ucfirst(strtolower($scriptCode)), $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $name;
    }

    /**
     * @param string $format
     * @param string $displayLocale
     * @return string|string[]
     */
    public function localeNames(string $format = 'array', ?string $displayLocale = null)
    {
        $locales = Intl\Locales::getNames($displayLocale);
        if($format == 'json') return json_encode($locales);
        return $locales;
    }

    /**
     * @param string $localeCode
     * @param string $displayLocale
     * @return string
     */
    public function localeNameFilter(string $localeCode, ?string $displayLocale = null)
    {
        $name = '';
        try {
            $name = Intl\Locales::getName($localeCode, $displayLocale);
        } catch(Intl\Exception\MissingResourceException $e) {

        }
        return $name;
    }


}
