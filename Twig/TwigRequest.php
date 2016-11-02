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
		//TODO no more used
        return array(
//            'getCookieDisclaimer' => new \Twig_SimpleFunction('getCookieDisclaimer', array($this->request, 'getCookieDisclaimer')),
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