<?php
namespace Keiwen\Cacofony\Translator;


use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class CodeTranslator
 *
 * @package Keiwen\Cacofony\Translator
 *
 * This class override default translator
 * Could be used to display translation code (id/domain/param)
 * instead of real translations (english/french/etc)
 * to identify specific translations
 */
class CodeTranslator extends Translator
{

    protected $localeTranscode;
    protected $transcodePattern;


    public function setTranslationParameters(string $localeCode, string $displayPattern)
    {
        $this->localeTranscode = $localeCode;
        $this->transcodePattern = $displayPattern;
    }


    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        if ($this->hasAskedForTransCode($locale)) {
            return $this->formatTransCode($id, $domain, $parameters);
        }

        return parent::trans($id, $parameters, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        if ($this->hasAskedForTransCode($locale)) {
            return $this->formatTransCode($id, $domain, $parameters);
        }

        return parent::transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * @param string $locale
     * @return bool
     */
    protected function hasAskedForTransCode($locale)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }
        $locale = explode('_', $locale);
        $locale = reset($locale);
        return $this->localeTranscode === $locale;
    }

    /**
     * @param string $id
     * @param string $domain
     * @return string
     */
    protected function formatTransCode($id, $domain = null, array $parameters = array())
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $implodedParam = implode(', ', array_keys($parameters));
        $display = $this->transcodePattern;
        $display = str_replace('{id}', $id, $display);
        $display = str_replace('{domain}', $domain, $display);
        $display = str_replace('{parameters}', $implodedParam, $display);

        return $display;
    }

}
