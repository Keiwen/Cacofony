<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Controller\ApiController;
use Keiwen\Cacofony\Controller\AppController;

class TwigController extends \Twig_Extension
{


    /** @var AppController */
    protected $appController;


    public function __construct(AppController $appController = null)
    {
        $this->appController = $appController;
    }


    /**
     * @return string
     */
	public function getName()
    {
		return 'caco_twig_controller';
	}

    /**
     * @return array
     */
	public function getFunctions()
    {
		return array(
            'getCookieDisclaimer' => new \Twig_SimpleFunction('getCookieDisclaimer', array($this->appController, 'getCookieDisclaimer')),
            'getCookieDisclaimerName' => new \Twig_SimpleFunction('getCookieDisclaimerName', array($this->appController, 'getCookieDisclaimerName')),
		);
	}


}