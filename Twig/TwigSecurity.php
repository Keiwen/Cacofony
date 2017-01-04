<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Security\RoleChecker;

class TwigSecurity extends \Twig_Extension
{

    protected $roleChecker;


    public function __construct(RoleChecker $roleChecker)
    {
        $this->roleChecker = $roleChecker;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'caco_twig_security_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hasRole', array($this->roleChecker, 'hasRole')),
        );
    }

}
