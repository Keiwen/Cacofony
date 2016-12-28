<?php

namespace Keiwen\Cacofony\Twig;


use Keiwen\Cacofony\Security\Annotation\RestrictToRole;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TwigSecurity extends \Twig_Extension
{

    protected $authorizationChecker;
    protected $rolePrefixes = array();

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, array $rolePrefixes = array())
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->rolePrefixes = $rolePrefixes;
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
            new \Twig_SimpleFunction('hasRole', array($this, 'hasRole')),
        );
    }


    /**
     * same rules than annotation
     * @see RestrictToRole
     * @param string $roleList contains list of role
     * @param bool   $mustHaveAll
     */
    public function hasRole(string $roleList, bool $mustHaveAll = false)
    {
        $values = array(
            'value' => $roleList,
            'additionalRolePrefix' => implode(',', $this->rolePrefixes),
            'mustHaveAll' => $mustHaveAll,
        );
        $anotation = new RestrictToRole($values);
        $expression = $anotation->getExpression();
        return $this->authorizationChecker->isGranted(array(new Expression($expression)));
    }

}
