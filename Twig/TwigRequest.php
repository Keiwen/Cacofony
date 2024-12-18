<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Http\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigRequest extends AbstractExtension
{

    /** @var Request */
    protected $request;
    protected $urlGenerator;


    public function __construct(UrlGeneratorInterface $urlGenerator, ?Request $request = null)
    {
        $this->urlGenerator = $urlGenerator;
        if(!$request) $request = new Request();
        $this->request = $request;

    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('getRequestCookie', array($this->request, 'getCookie')),
            new TwigFunction('getSelfUrl', array($this->request, 'getUrl')),
            new TwigFunction('getCookieDisclaimer', array($this->request, 'getCookieDisclaimer')),
            new TwigFunction('getCookieDisclaimerName', array($this->request, 'getCookieDisclaimerName')),
            new TwigFunction('getRoute', array($this->request, 'getRouteName')),
            new TwigFunction('isRouteActive', array($this, 'isRouteActive')),
            new TwigFunction('checkActiveRoute', array($this, 'checkActiveRoute')),
            new TwigFunction('getLocalizedUrl', array($this, 'getLocalizedUrl')),
        );
    }


    /**
     * @param string $routeName
     * @return bool
     */
    public function isRouteActive(string $routeName)
    {
        return $this->request->getRouteName() == $routeName;
    }

    /**
     * @param string $routeName
     * @param string $cssClass
     * @return string
     */
    public function checkActiveRoute(string $routeName, string $cssClass = 'active')
    {
        return $this->isRouteActive($routeName) ? $cssClass : '';
    }


    /**
     * @param string $locale
     * @param bool $relative
     * @return string
     */
    public function getLocalizedUrl(string $locale, bool $relative = false): string
    {
        $routeName = $this->request->getRouteName();
        $routeParameters = $this->request->getRouteParams();
        $routeParameters['_locale'] = $locale;
        return $this->urlGenerator->generate($routeName, $routeParameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);

    }


}
