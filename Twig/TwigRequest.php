<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Http\Request;

class TwigRequest extends \Twig_Extension
{

    /** @var Request */
    protected $request;


    public function __construct(Request $request = null)
    {
        if(!$request) $request = new Request();
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'requestCookie' => new \Twig_SimpleFunction('getRequestCookie', array($this->request, 'getCookie')),
            'selfUrl' => new \Twig_SimpleFunction('getSelfUrl', array($this->request, 'getUrl')),
            'getCookieDisclaimer' => new \Twig_SimpleFunction('getCookieDisclaimer', array($this->request, 'getCookieDisclaimer')),
            'getCookieDisclaimerName' => new \Twig_SimpleFunction('getCookieDisclaimerName', array($this->request, 'getCookieDisclaimerName')),
        );
    }


    /**
     * @return string
     */
	public function getName()
    {
		return 'caco_twig_request';
	}


}
