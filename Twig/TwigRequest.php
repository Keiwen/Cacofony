<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Http\Request;

class TwigRequest extends \Twig_Extension
{

    /** @var Request */
    protected $request;


    public function __construct(Request $request = null)
    {
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