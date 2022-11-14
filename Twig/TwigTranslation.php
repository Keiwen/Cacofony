<?php

namespace Keiwen\Cacofony\Twig;


use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorTrait;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\Environment as TwigEnvironment;

/**
 * Class TwigTranslation
 * Override default TranslationExtension to manage non breakable spaces that could occurs with punctuation
 *
 * @package Keiwen\Cacofony\Twig
 */
class TwigTranslation extends AbstractExtension
{

    protected $twig;
    protected $requestStack;
    protected $translator;

    /** @var array List of punctuation mark that MUST have nb-space before */
    protected static $spaceBeforePunctuation = array(
        'fr' => array(':', '?', '!', ';', '»'),
    );

    /** @var array List of punctuation mark that MUST have nb-space after */
    protected static $spaceAfterPunctuation = array(
        'fr' => array('«'),
    );


    public function __construct(TranslatorInterface $translator = null, RequestStack $requestStack = null, TwigEnvironment $twig = null)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('trans', array($this, 'trans'), array('is_safe' => array('html'))),
            new TwigFilter('hasTrans', array($this, 'hasTrans'), array('is_safe' => array('html'))),
            new TwigFilter('punctuate', array($this, 'punctuate'), array('is_safe' => array('html'))),
            new TwigFilter('label', array($this, 'label'), array('is_safe' => array('html'))),
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
     * Add punctuation at the end of text.
     * Typically used for adding 2 dots for labels (':')
     * Could add nb-space before mark according to settings
     * @param string $text
     * @param string $mark
     * @param string $locale
     * @return string
     */
    public function punctuate(string $text, string $mark, string $locale = '')
    {
        if(empty($locale)) $locale = $this->detectMainLocale();
        if(!empty(static::$spaceBeforePunctuation[$locale])) {
            if(in_array($mark, static::$spaceBeforePunctuation[$locale])) $text .= '&nbsp;';
        }
        return $text . $mark;
    }

    /**
     * Add 2 dots at the end of text (':')
     * @param string $text
     * @param string $locale
     * @return string
     */
    public function label(string $text, string $locale = '')
    {
        if(empty($locale)) $locale = $this->detectMainLocale();
        return $this->punctuate($text, ':', $locale);
    }


    /**
     * Translate text and add nb-spaces if needed. Add twig globals as parameter value
     * @param $message
     * @param array $arguments Can be the locale as a string when $message is a TranslatableInterface
     * @param string|null $domain
     * @param string|null $locale
     * @param int|null $count
     * @param bool $nbsp
     * @return string
     */
    public function trans($message, $arguments = array(), string $domain = null, string $locale = null, int $count = null, $nbsp = true): string
    {
        if(!($message instanceof TranslatableInterface) && !empty($this->twig)) {
            //add globals twig variable to trans parameter if scalar
            $twigGlobals = $this->twig->getGlobals();
            foreach($twigGlobals as $key => $twigGlobal) {
                if(is_scalar($twigGlobal) && !isset($arguments[$key])) {
                    $arguments[$key] = $twigGlobal;
                }
            }
        }

        if(empty($domain)) $domain = null;

        // START OF INITIAL SF METHOD
        if ($message instanceof TranslatableInterface) {
            if ([] !== $arguments && !\is_string($arguments)) {
                throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be a locale passed as a string when the message is a "%s", "%s" given.', __METHOD__, TranslatableInterface::class, get_debug_type($arguments)));
            }

            $trans = $message->trans($this->getTranslator(), $locale ?? (\is_string($arguments) ? $arguments : null));
        } else {
            if (!\is_array($arguments)) {
                throw new \TypeError(sprintf('Unless the message is a "%s", argument 2 passed to "%s()" must be an array of parameters, "%s" given.', TranslatableInterface::class, __METHOD__, get_debug_type($arguments)));
            }

            if ('' === $message = (string) $message) {
                return '';
            }

            if (null !== $count) {
                $arguments['%count%'] = $count;
            }

            $trans = $this->getTranslator()->trans($message, $arguments, $domain, $locale);
        }
        // END OF INITIAL SF METHOD


        if(!$nbsp) return $trans;

        //get main locale to get settings
        $locale = $this->detectMainLocale($locale);

        if(!empty(static::$spaceBeforePunctuation[$locale])) {
            foreach(static::$spaceBeforePunctuation[$locale] as $mark) {
                $trans = str_replace(' ' . $mark, '&nbsp;' . $mark, $trans);
            }
        }

        if(!empty(static::$spaceAfterPunctuation[$locale])) {
            foreach(static::$spaceAfterPunctuation[$locale] as $mark) {
                $trans = str_replace($mark . ' ', $mark . '&nbsp;', $trans);
            }
        }
        return $trans;
    }


    /**
     * Check if translation found (return false if translation equal to message)
     * @param $message
     * @param string|null $domain
     * @param string|null $locale
     * @return bool
     */
    public function hasTrans($message, string $domain = null, string $locale = null)
    {
        $trans = $this->trans($message, array(), $domain, $locale);
        return $trans != $message;
    }


    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        if (null === $this->translator) {
            if (!interface_exists(TranslatorInterface::class)) {
                throw new \LogicException(sprintf('You cannot use the "%s" if the Translation Contracts are not available. Try running "composer require symfony/translation".', __CLASS__));
            }

            $this->translator = new class() implements TranslatorInterface {
                use TranslatorTrait;
            };
        }

        return $this->translator;
    }

    /**
     * Add unit at the end of text.
     * @param string $text
     * @param string $unit
     * @return string
     */
    public function unit($text, string $unit)
    {
        return $text . '&nbsp;' . $unit;
    }

}
