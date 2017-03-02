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
    /** @var TwigTranslation */
    private $twigTranslationExtension;

    /**
     * @param string          $defaultLocale
     * @param TwigTranslation $twigTranslationExtension
     */
    public function __construct(string $defaultLocale, TranslationExtension $twigTranslationExtension = null)
    {
        $this->defaultLocale = $defaultLocale;
        if($twigTranslationExtension instanceof TwigTranslation) {
            //ignore if default SF service not overridden
            $this->twigTranslationExtension = $twigTranslationExtension;
        }
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
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $locale = $request->getSession()->get('_locale', $this->defaultLocale);
            $request->setLocale($locale);
        }

        if($this->twigTranslationExtension) {
            //set locale for twig translation extension
            $this->twigTranslationExtension->setLocale($locale);
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
}