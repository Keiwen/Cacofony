<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Twig\TwigTranslation;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    /**
     * @param string          $defaultLocale
     * @param TwigTranslation $twigTranslationExtension
     */
    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if($locale = $request->attributes->get('_locale')) {
            $locale = static::convertLocale($locale);
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $locale = $request->getSession()->get('_locale', $this->defaultLocale);
            $locale = static::convertLocale($locale);
            $request->setLocale($locale);
        }

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default symfony Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }



    /**
     * Norms defined locale as language-region, symfony use language_region
     * @param string $locale
     * @return string
     */
    public static function convertLocale($locale)
    {
        return str_replace('-', '_', $locale);
    }

}
