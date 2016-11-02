<?php

namespace Keiwen\Cacofony\Http;


use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Response extends \Symfony\Component\HttpFoundation\Response
{

    /**
     * @param string $name
     * @param mixed $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function setCookie(string $name,
                              $value = 1,
                              int $expire = 0,
                              string $path = '/',
                              string $domain = null,
                              bool $secure = false,
                              bool $httpOnly = true)
    {
        if(is_array($value)) {
            $value = implode(';', $value);
        }
        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        $this->headers->setCookie($cookie);
    }


    /**
     * @param string $name
     */
    public function removeCookie(string $name)
    {
        $this->headers->clearCookie($name);
    }


    /**
     *
     */
    public function getCookies()
    {
        $this->headers->getCookies();
    }


    /**
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    public function generateRedirect(string $url, int $status)
    {
        $redirect = new RedirectResponse($url, $status);
        /** @var Cookie[] $cookies */
        $cookies = $this->headers->getCookies();
        foreach($cookies as $cookie) {
            $redirect->headers->setCookie($cookie);
        }
        return $redirect;
    }


}