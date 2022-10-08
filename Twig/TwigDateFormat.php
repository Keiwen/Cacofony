<?php

namespace Keiwen\Cacofony\Twig;


use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFilter;
use Keiwen\Utils\Format\DateFormat;

/**
 * Class TwigDateFormat
 *
 * @package Keiwen\Cacofony\Twig
 */
class TwigDateFormat extends AbstractExtension
{

    protected $requestStack;

    public function __construct(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('formatDateFull', array($this, 'formatDateFull'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateLong', array($this, 'formatDateLong'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateMedium', array($this, 'formatDateMedium'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateShort', array($this, 'formatDateShort'), array('is_safe' => array('html'))),
            new TwigFilter('formatTimeFull', array($this, 'formatTimeFull'), array('is_safe' => array('html'))),
            new TwigFilter('formatTimeLong', array($this, 'formatTimeLong'), array('is_safe' => array('html'))),
            new TwigFilter('formatTimeMedium', array($this, 'formatTimeMedium'), array('is_safe' => array('html'))),
            new TwigFilter('formatTimeShort', array($this, 'formatTimeShort'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateAndTimeFull', array($this, 'formatDateAndTimeFull'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateAndTimeLong', array($this, 'formatDateAndTimeLong'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateAndTimeMedium', array($this, 'formatDateAndTimeMedium'), array('is_safe' => array('html'))),
            new TwigFilter('formatDateAndTimeShort', array($this, 'formatDateAndTimeShort'), array('is_safe' => array('html'))),
            new TwigFilter('formatIsoDate', array($this, 'formatIsoDate'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string|null $locale
     * @return string
     */
    protected function detectMainLocale(string $locale = null)
    {
        if(empty($locale)) $locale = $this->requestStack->getMainRequest()->getLocale();
        if(strpos($locale, '_') !== false) {
            $locale = explode('_', $locale);
            $locale = reset($locale);
        }
        return $locale;
    }

    /**
     * @param string|null $locale
     * @return DateFormat
     */
    protected function getDateFormatter(string $locale = null)
    {
        return new DateFormat($this->detectMainLocale($locale));
    }
    

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateFull($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateFull($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateLong($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateLong($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateMedium($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateMedium($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateShort($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateShort($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatTimeFull($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatTimeFull($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatTimeLong($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatTimeLong($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatTimeMedium($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatTimeMedium($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatTimeShort($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatTimeShort($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateAndTimeFull($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateAndTimeFull($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateAndTimeLong($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateAndTimeLong($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateAndTimeMedium($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateAndTimeMedium($date);
    }

    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param string|null $locale
     * @return string
     */
    public function formatDateAndTimeShort($date = null, string $locale = null)
    {
        return $this->getDateFormatter($locale)->formatDateAndTimeShort($date);
    }
    
    /**
     * @param int|string|\DateTimeInterface $date timestamp, string or DateTimeInterface
     * @param bool $inclTime
     * @return string
     */
    public function formatIsoDate($date = null, bool $inclTime = false)
    {
        $isoDate = $this->getDateFormatter()->formatDateIso($date);
        if ($inclTime) {
            $isoDate .= ' ' . $this->getDateFormatter()->formatTimeIso($date);
        }
        return $isoDate;
    }

    
}
