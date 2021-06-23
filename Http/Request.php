<?php

namespace Keiwen\Cacofony\Http;


use Keiwen\Cacofony\Exception\RequestNotFoundException;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\RequestStack;

class Request extends \Symfony\Component\HttpFoundation\Request
{

    protected $retrievedParameters = array();

    /** @var StringSanitizer $stringSanitizer */
    protected $stringSanitizer;

    public const COOKIE_DISCLAIMER = 'cookie_disclaimer';



    /**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     * @param StringSanitizer      $stringSanitizer
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null, StringSanitizer $stringSanitizer = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        if ($stringSanitizer == null) $stringSanitizer = new StringSanitizer();
        $this->stringSanitizer = $stringSanitizer;
    }


    /**
     * @param RequestStack $requestStack
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws RequestNotFoundException
     */
    public static function retrieveRequestFromStack(RequestStack $requestStack)
    {
        if(!empty($requestStack)) {
            $request = $requestStack->getCurrentRequest();
            if($request instanceof Request) {
                return $request;
            }
            throw new RequestNotFoundException('Cacofony request not found, request type is ' . get_class($request));
        }
        throw new RequestNotFoundException('Request stack is empty');
    }


    /**
     * @param string $name
     * @param string $type
     * @param mixed $default
     * @return mixed
     */
    public function getRequestParam(string $name,
                                    string $type = StringSanitizer::FILTER_DEFAULT,
                                    $default = null)
    {
        $value = $this->stringSanitizer->get($this->get($name, $default), $type);
        $this->addRetrievedParameter($name, $value);
        return $value;
    }


    /**
     * keep history of parameters retrieved from request
     * @param string $name
     * @param mixed  $value
     */
    public function addRetrievedParameter(string $name, $value)
    {
        $this->retrievedParameters[$name] = $value;
    }


    /**
     * @return array
     */
    public function getRetrievedParameters()
    {
        return $this->retrievedParameters;
    }


    /**
     * @param string $name
     * @param string $type
     * @param mixed $default
     * @return mixed
     */
    public function getCookie(string $name,
                              string $type = StringSanitizer::FILTER_DEFAULT,
                              $default = null)
    {
        if(!$this->cookies->has($name)) return $default;
        $cookie = $this->cookies->get($name, $default);
        return $this->stringSanitizer->get($cookie, $type);
    }

    /**
     * @return bool
     */
    public function getCookieDisclaimer()
    {
        return $this->getCookie($this->getCookieDisclaimerName(), StringSanitizer::FILTER_BOOLEAN, false);
    }

    /**
     * @return string
     */
    public function getCookieDisclaimerName()
    {
        return static::COOKIE_DISCLAIMER;
    }



    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->get('_route');
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->get('_route_params');
    }

    /**
     * @param bool $includeParam
     * @param bool $absolute
     * @return mixed|string
     */
    public function getUrl(bool $includeParam = false, bool $absolute = false)
    {
        if($absolute) {
            $url = $this->getUri();
        } else {
            $url = $this->getRequestUri();
        }
        if(!$includeParam) {
            $exploded = explode('?', $url);
            $url = reset($exploded);
        }
        return $url;
    }



}
