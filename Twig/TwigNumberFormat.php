<?php

namespace Keiwen\Cacofony\Twig;


use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFilter;
use Keiwen\Utils\Format\NumberFormat;

/**
 * Class TwigNumberFormat
 *
 * @package Keiwen\Cacofony\Twig
 */
class TwigNumberFormat extends AbstractExtension
{

    protected $requestStack;

    public function __construct(?RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('formatCurrency', array($this, 'formatCurrency'), array('is_safe' => array('html'))),
            new TwigFilter('formatDecimal', array($this, 'formatDecimal'), array('is_safe' => array('html'))),
            new TwigFilter('formatPercent', array($this, 'formatPercent'), array('is_safe' => array('html'))),
            new TwigFilter('formatScientific', array($this, 'formatScientific'), array('is_safe' => array('html'))),
            new TwigFilter('formatSpellout', array($this, 'formatSpellout'), array('is_safe' => array('html'))),
            new TwigFilter('formatOrdinal', array($this, 'formatOrdinal'), array('is_safe' => array('html'))),
            new TwigFilter('formatDuration', array($this, 'formatDuration'), array('is_safe' => array('html'))),
            new TwigFilter('formatUnit', array($this, 'formatUnit'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string|null $locale
     * @return string
     */
    protected function detectMainLocale(?string $locale = null)
    {
        if(empty($locale)) {
            if (!$this->requestStack) return '';
            $locale = $this->requestStack->getMainRequest()->getLocale();
        }
        if(strpos($locale, '_') !== false) {
            $locale = explode('_', $locale);
            $locale = reset($locale);
        }
        return $locale;
    }

    /**
     * @param string|null $locale
     * @return NumberFormat
     */
    protected function getNumberFormatter(?string $locale = null)
    {
        return new NumberFormat($this->detectMainLocale($locale));
    }


    /**
     * @param int|float $value
     * @param string $currency
     * @param string|null $locale
     * @return string
     */
    public function formatCurrency($value = null, string $currency = 'EUR', ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatCurrency((float) $value, $currency);
    }


    /**
     * @param int|float $value
     * @param int|null $maxFractionDigits
     * @param int|null $fractionDigits
     * @param string|null $locale
     * @return string
     */
    public function formatDecimal($value = null, ?int $maxFractionDigits = null, ?int $fractionDigits = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatDecimal((float) $value, $maxFractionDigits, $fractionDigits);
    }


    /**
     * @param int|float $value
     * @param int|null $maxFractionDigits
     * @param int|null $fractionDigits
     * @param string|null $locale
     * @return string
     */
    public function formatPercent($value = null, ?int $maxFractionDigits = null, ?int $fractionDigits = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatPercent((float) $value, $maxFractionDigits, $fractionDigits);
    }


    /**
     * @param int|float $value
     * @param int|null $maxFractionDigits
     * @param int|null $fractionDigits
     * @param string|null $locale
     * @return string
     */
    public function formatScientific($value = null, ?int $maxFractionDigits = null, ?int $fractionDigits = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatScientific((float) $value, $maxFractionDigits, $fractionDigits);
    }


    /**
     * @param int|float $value
     * @param int|null $maxFractionDigits
     * @param string|null $locale
     * @return string
     */
    public function formatSpellout($value = null, ?int $maxFractionDigits = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatSpellout((float) $value, $maxFractionDigits);
    }

    /**
     * @param int|float $value
     * @param string|null $locale
     * @return string
     */
    public function formatOrdinal($value = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatOrdinal((float) $value);
    }

    /**
     * @param int|float $value
     * @param string|null $locale
     * @return string
     */
    public function formatDuration($value = null, ?string $locale = null)
    {
        return $this->getNumberFormatter($locale)->formatDuration((float) $value);
    }


    /**
     * Add unit at the end of text.
     * @param string $text
     * @param string $unit
     * @return string
     */
    public function formatUnit($text, string $unit)
    {
        return $text . '&nbsp;' . $unit;
    }


}
