<?php

namespace Keiwen\Cacofony\Twig;


use Symfony\Bridge\Twig\Extension\TranslationExtension;

/**
 * Class TwigTranslation
 * Override default TranslationExtension to manage non breakable spaces that could occurs with punctuation
 * To override trans filter, this extension should be registered with default service id "twig.extension.trans"
 *
 * @package Keiwen\Cacofony\Twig
 */
class TwigTranslation extends TranslationExtension
{

    /** @var string Should be managed on request with a listener */
    protected $locale = 'en';

    /** @var array List of punctuation mark that MUST have nb-space before */
    protected static $spaceBeforePunctuation = array(
        'fr' => array(':', '?', '!', ';', '»'),
    );

    /** @var array List of punctuation mark that MUST have nb-space after */
    protected static $spaceAfterPunctuation = array(
        'fr' => array('«'),
    );


    /**
     * @return string
     */
    public function getName()
    {
        return 'caco_portal_twig_translation';
    }


    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = parent::getFilters();
        $filters[] = new \Twig_SimpleFilter('trans', array($this, 'trans'), array('is_safe' => array('html')));
        $filters[] = new \Twig_SimpleFilter('punctuate', array($this, 'punctuate'), array('is_safe' => array('html')));
        $filters[] = new \Twig_SimpleFilter('label', array($this, 'label'), array('is_safe' => array('html')));
        return $filters;
    }


    /**
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        if(strpos($locale, '_') !== false) {
            $locale = explode('_', $locale);
            $locale = reset($locale);
        }
        $this->locale = $locale;
    }

    /**
     * Add punctuation at the end of text.
     * Typically used for adding 2 dots for labels (':')
     * Could add nb-space before mark according to settings
     * @param string $text
     * @param string $mark
     * @return string
     */
    public function punctuate(string $text, string $mark)
    {
        if(!empty(static::$spaceBeforePunctuation[$this->locale])) {
            if(in_array($mark, static::$spaceBeforePunctuation[$this->locale])) $text .= '&nbsp;';
        }
        return $text . $mark;
    }

    /**
     * Add 2 dots at the end of text (':')
     * @param string $text
     * @return string
     */
    public function label(string $text)
    {
        return $this->punctuate($text, ':');
    }


    /**
     * @inheritdoc
     * Translate text and add nb-spaces if needed
     * @param string      $id
     * @param array       $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $trans = parent::trans($id, $parameters, $domain, $locale);
        //get main locale to get settings
        if(empty($locale)) {
            $locale = $this->locale;
        } else if(strpos($locale, '_') !== false) {
            $locale = explode('_', $locale);
            $locale = reset($locale);
        }

        if(empty(static::$spaceBeforePunctuation[$locale])) {
            return $trans;
        }
        foreach(static::$spaceBeforePunctuation[$locale] as $mark) {
            $trans = str_replace(' ' . $mark, '&nbsp;' . $mark, $trans);
        }
        foreach(static::$spaceAfterPunctuation[$locale] as $mark) {
            $trans = str_replace($mark . ' ', $mark . '&nbsp;', $trans);
        }
        return $trans;
    }
}