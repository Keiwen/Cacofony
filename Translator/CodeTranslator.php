<?php
namespace Keiwen\Cacofony\Translator;


use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class CodeTranslator
 *
 * @package Keiwen\Cacofony\Translator
 *
 * This class override default translator
 * Could be used to display translation code (message/domain/arguments)
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
    public function trans(?string $id, array $parameters = array(), ?string $domain = null, ?string $locale = null): string
    {
        if ($this->hasAskedForTransCode($locale)) {
            return $this->formatTransCode($id, $domain, $parameters);
        }

        return parent::trans($id, $parameters, $domain, $locale);
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
     * @param string $message
     * @param string $domain
     * @return string
     */
    protected function formatTransCode($message, $domain = null, array $arguments = array())
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $implodedArg= implode(', ', array_keys($arguments));
        $display = $this->transcodePattern;
        $display = str_replace('{message}', $message, $display);
        $display = str_replace('{domain}', $domain, $display);
        $display = str_replace('{arguments}', $implodedArg, $display);

        return $display;
    }

}
