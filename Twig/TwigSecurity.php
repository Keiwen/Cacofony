<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Security\RoleChecker;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigSecurity extends AbstractExtension
{

    protected $roleChecker;


    public function __construct(RoleChecker $roleChecker)
    {
        $this->roleChecker = $roleChecker;
    }


    public function getFunctions(): array
    {
        return array(
            new TwigFunction('hasRole', array($this->roleChecker, 'hasRole')),
        );
    }

}
